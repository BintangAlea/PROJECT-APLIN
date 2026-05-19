<?php

namespace App\Controllers;

use App\Core\ApiResponse;
use App\Core\Database;

/**
 * API Inventaris Gudang Controller
 * Handle inventory management with:
 * 1. Auto stock deduction from BOM when order placed
 * 2. Manual extra material usage tracking
 * 3. Minimum stock alerts
 */
class ApiInventarisController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        header('Content-Type: application/json');
    }

    /**
     * GET /api/inventory
     * Get all inventory items
     * 
     * Query params:
     * - page: pagination
     * - limit: items per page
     * - low_stock: true untuk hanya tampilkan yang low stock
     */
    public function getAll()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $offset = ($page - 1) * $limit;
        $lowStock = $_GET['low_stock'] ?? false;

        $where = '';
        if ($lowStock === 'true') {
            $where = 'WHERE stock_qty <= min_stock';
        }

        // Count total
        $countStmt = $this->db->query("SELECT COUNT(*) as count FROM inventories {$where}");
        $total = $countStmt->fetch()['count'];

        // Get items
        $stmt = $this->db->query(
            "SELECT * FROM inventories {$where}
             ORDER BY item_name ASC
             LIMIT {$limit} OFFSET {$offset}"
        );
        $items = $stmt->fetchAll();

        echo ApiResponse::paginated($items, $total, $page, $limit, 'Inventory items retrieved');
    }

    /**
     * GET /api/inventory/:item_id
     * Get inventory item details
     */
    public function getItem($itemId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $stmt = $this->db->prepare('SELECT * FROM inventories WHERE item_id = :item_id');
        $stmt->execute([':item_id' => $itemId]);
        $item = $stmt->fetch();

        if (!$item) {
            echo ApiResponse::notFound('Inventory item not found');
            return;
        }

        // Calculate stock status
        $stockStatus = 'Normal';
        if ($item['stock_qty'] <= $item['min_stock']) {
            $stockStatus = 'Low Stock - Alert!';
        } elseif ($item['stock_qty'] <= $item['min_stock'] * 1.5) {
            $stockStatus = 'Warning';
        }

        echo ApiResponse::success([
            ...array_values((array)$item),
            'stock_status' => $stockStatus
        ], 'Item details retrieved', 200);
    }

    /**
     * POST /api/inventory/deduct-from-bom
     * Auto deduct stock based on menu's BOM
     * When cafe order is placed
     * 
     * Request body:
     * {
     *   "menu_id": "MENU001",
     *   "qty": 2
     * }
     */
    public function deductFromBOM()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['menu_id'])) {
            echo ApiResponse::validationError(['menu_id' => 'Menu ID required']);
            return;
        }

        if (empty($input['qty']) || $input['qty'] < 1) {
            echo ApiResponse::validationError(['qty' => 'Quantity must be > 0']);
            return;
        }

        // Get menu and its BOM
        $menu = $this->db->prepare('SELECT * FROM menus WHERE menu_id = :menu_id');
        $menu->execute([':menu_id' => $input['menu_id']]);
        $menuData = $menu->fetch();

        if (!$menuData) {
            echo ApiResponse::notFound('Menu not found');
            return;
        }

        if (!$menuData['bom_recipe_id']) {
            echo ApiResponse::error('Menu has no BOM defined', 400);
            return;
        }

        // Get BOM items
        $bom = $this->db->prepare(
            'SELECT bd.*, i.item_name, i.stock_qty 
             FROM bom_details bd
             JOIN inventories i ON bd.item_id = i.item_id
             WHERE bd.bom_recipe_id = :bom_id'
        );
        $bom->execute([':bom_id' => $menuData['bom_recipe_id']]);
        $bomItems = $bom->fetchAll();

        $deductedItems = [];
        $insufficientStock = [];

        try {
            // Check all items have enough stock
            foreach ($bomItems as $bomItem) {
                $requiredQty = $bomItem['quantity_required'] * $input['qty'];
                
                if ($bomItem['stock_qty'] < $requiredQty) {
                    $insufficientStock[] = [
                        'item_id' => $bomItem['item_id'],
                        'item_name' => $bomItem['item_name'],
                        'required' => $requiredQty,
                        'available' => $bomItem['stock_qty']
                    ];
                }
            }

            if (!empty($insufficientStock)) {
                echo ApiResponse::error(
                    'Insufficient stock for some items',
                    400,
                    $insufficientStock
                );
                return;
            }

            // Deduct all items
            foreach ($bomItems as $bomItem) {
                $requiredQty = $bomItem['quantity_required'] * $input['qty'];
                
                $updateStmt = $this->db->prepare(
                    'UPDATE inventories SET stock_qty = stock_qty - :qty WHERE item_id = :item_id'
                );
                $updateStmt->execute([
                    ':qty' => $requiredQty,
                    ':item_id' => $bomItem['item_id']
                ]);

                $deductedItems[] = [
                    'item_id' => $bomItem['item_id'],
                    'item_name' => $bomItem['item_name'],
                    'quantity_deducted' => $requiredQty,
                    'remaining_stock' => $bomItem['stock_qty'] - $requiredQty
                ];
            }

            echo ApiResponse::success([
                'menu_id' => $input['menu_id'],
                'order_qty' => $input['qty'],
                'deducted_items' => $deductedItems
            ], 'Stock deducted from BOM successfully', 200);
        } catch (\Exception $e) {
            echo ApiResponse::error('Failed to deduct stock: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/inventory/use-extra-material
     * Record extra material usage by beautician
     * 
     * Request body:
     * {
     *   "res_id": 1,
     *   "item_id": 5,
     *   "qty_used": 2
     * }
     */
    public function useExtraMaterial()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $errors = [];
        if (empty($input['res_id'])) $errors['res_id'] = 'Reservation ID required';
        if (empty($input['item_id'])) $errors['item_id'] = 'Item ID required';
        if (empty($input['qty_used']) || $input['qty_used'] < 0) $errors['qty_used'] = 'Quantity used required';

        if (!empty($errors)) {
            echo ApiResponse::validationError($errors);
            return;
        }

        // Check inventory
        $inv = $this->db->prepare('SELECT * FROM inventories WHERE item_id = :item_id');
        $inv->execute([':item_id' => $input['item_id']]);
        $inventory = $inv->fetch();

        if (!$inventory) {
            echo ApiResponse::notFound('Inventory item not found');
            return;
        }

        if ($inventory['stock_qty'] < $input['qty_used']) {
            echo ApiResponse::error(
                'Insufficient stock. Available: ' . $inventory['stock_qty'] . ', Need: ' . $input['qty_used'],
                400
            );
            return;
        }

        try {
            // Record usage
            $record = $this->db->prepare(
                'INSERT INTO extra_material_usages (res_id, item_id, qty_used)
                 VALUES (:res_id, :item_id, :qty_used)'
            );
            $record->execute([
                ':res_id' => $input['res_id'],
                ':item_id' => $input['item_id'],
                ':qty_used' => $input['qty_used']
            ]);

            // Deduct stock
            $deduct = $this->db->prepare(
                'UPDATE inventories SET stock_qty = stock_qty - :qty WHERE item_id = :item_id'
            );
            $deduct->execute([
                ':qty' => $input['qty_used'],
                ':item_id' => $input['item_id']
            ]);

            // Calculate charge
            $chargeAmount = $inventory['extra_charge_per_unit'] * $input['qty_used'];

            echo ApiResponse::success([
                'usage_id' => $this->db->lastInsertId(),
                'res_id' => $input['res_id'],
                'item_id' => $input['item_id'],
                'item_name' => $inventory['item_name'],
                'qty_used' => $input['qty_used'],
                'unit_price' => $inventory['extra_charge_per_unit'],
                'charge_amount' => $chargeAmount,
                'remaining_stock' => $inventory['stock_qty'] - $input['qty_used']
            ], 'Extra material usage recorded', 201);
        } catch (\Exception $e) {
            echo ApiResponse::error('Failed to record usage: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/inventory/low-stock-alerts
     * Get low stock alerts (for admin dashboard)
     */
    public function getLowStockAlerts()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ApiResponse::error('Method not allowed', 405);
            return;
        }

        $alerts = $this->db->query(
            'SELECT item_id, item_name, stock_qty, min_stock, unit, 
                    (min_stock - stock_qty) as shortage,
                    CASE 
                        WHEN stock_qty <= min_stock THEN "Critical"
                        WHEN stock_qty <= min_stock * 1.5 THEN "Warning"
                        ELSE "Normal"
                    END as alert_level
             FROM inventories
             WHERE stock_qty <= min_stock * 1.5
             ORDER BY alert_level DESC, shortage DESC'
        );
        $alertItems = $alerts->fetchAll();

        echo ApiResponse::success([
            'total_alerts' => count($alertItems),
            'critical_count' => count(array_filter($alertItems, fn($a) => $a['alert_level'] === 'Critical')),
            'warning_count' => count(array_filter($alertItems, fn($a) => $a['alert_level'] === 'Warning')),
            'alerts' => $alertItems
        ], 'Low stock alerts retrieved', 200);
    }
}
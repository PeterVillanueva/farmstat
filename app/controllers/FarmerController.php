<?php
/**
 * Farmer Controller
 * PHP 8 Compatible
 */

class FarmerController extends Controller {
    private Farmer $farmerModel;

    public function __construct() {
        parent::__construct();
        require_once MODELS_PATH . '/Farmer.php';
        $this->farmerModel = new Farmer();
    }

    public function index(): void {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
            $this->delete();
            return;
        }

        $farmers = $this->farmerModel->getAll();
        $this->json(['success' => true, 'data' => $farmers]);
    }

    public function create(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => $_POST['full_name'] ?? '',
                'years_experience' => (int)($_POST['years_experience'] ?? 0),
                'farm_location' => $_POST['farm_location'] ?? null,
                'farm_size' => $_POST['farm_size'] ? (float)$_POST['farm_size'] : null,
                'farming_method' => $_POST['farming_method'] ?? null,
                'land_ownership' => $_POST['land_ownership'] ?? null,
                'varieties' => $_POST['varieties'] ?? null
            ];

            try {
                $id = $this->farmerModel->create($data);
                $this->json(['success' => true, 'message' => 'Farmer created successfully', 'id' => $id]);
            } catch (Exception $e) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        } else {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }
    }

    public function delete(): void {
        $this->requireAuth();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        
        if ($id > 0) {
            $this->farmerModel->delete($id);
            $this->json(['success' => true, 'message' => 'Farmer deleted successfully']);
        } else {
            $this->json(['success' => false, 'error' => 'Invalid farmer ID'], 400);
        }
    }
}


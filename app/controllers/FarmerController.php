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
        $this->json(['success' => true, 'farmers' => $farmers]);
    }

    public function create(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            // Handle varieties - could be comma-separated string or array
            $varieties = $input['varieties'] ?? $input['rice_varieties'] ?? null;
            if (is_string($varieties)) {
                $varieties = explode(',', $varieties);
            }
            
            $data = [
                'full_name' => $input['full_name'] ?? $input['farmerName'] ?? '',
                'years_experience' => (int)($input['years_experience'] ?? $input['experience_years'] ?? $input['farmerExperience'] ?? 0),
                'farm_location' => $input['farm_location'] ?? $input['location'] ?? $input['farmerLocation'] ?? null,
                'farm_size' => isset($input['farm_size']) ? (float)$input['farm_size'] : (isset($input['farmSize']) ? (float)$input['farmSize'] : null),
                'farming_method' => $input['farming_method'] ?? $input['farmingMethod'] ?? null,
                'land_ownership' => $input['land_ownership'] ?? $input['landOwnership'] ?? null,
                'varieties' => $varieties
            ];

            try {
                $id = $this->farmerModel->create($data);
                $farmer = $this->farmerModel->findById($id);
                $this->json(['success' => true, 'message' => 'Farmer created successfully', 'id' => $id, 'farmer' => $farmer]);
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


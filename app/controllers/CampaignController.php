<?php
/**
 * Campaign Controller
 * PHP 8 Compatible
 */

class CampaignController extends Controller {
    private Campaign $campaignModel;
    private Farmer $farmerModel;

    public function __construct() {
        parent::__construct();
        require_once MODELS_PATH . '/Campaign.php';
        require_once MODELS_PATH . '/Farmer.php';
        $this->campaignModel = new Campaign();
        $this->farmerModel = new Farmer();
    }

    public function index(): void {
        $this->requireAuth();
        
        $campaigns = $this->campaignModel->getAll();
        $this->json(['success' => true, 'data' => $campaigns]);
    }

    public function create(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $data = [
                'title' => $input['title'] ?? '',
                'description' => $input['description'] ?? '',
                'campaign_type' => $input['campaign_type'] ?? '',
                'funding_goal' => (float)($input['funding_goal'] ?? 0),
                'deadline' => $input['deadline'] ?? null,
                'farmer_id' => $input['farmer_id'] ?? null,
                'status' => 'active'
            ];

            // Assign to random farmer if not specified
            if (!$data['farmer_id']) {
                $farmers = $this->farmerModel->getAll(1);
                $data['farmer_id'] = !empty($farmers) ? $farmers[0]['id'] : null;
            }

            try {
                $id = $this->campaignModel->create($data);
                $this->json(['success' => true, 'message' => 'Campaign created successfully', 'id' => $id]);
            } catch (Exception $e) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 400);
            }
        } else {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }
    }
}


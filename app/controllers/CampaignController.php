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
        // Format campaigns for frontend
        $formattedCampaigns = array_map(function($campaign) {
            return [
                'id' => $campaign['id'],
                'title' => $campaign['title'],
                'description' => $campaign['description'],
                'campaign_type' => $campaign['campaign_type'],
                'funding_goal' => (float)($campaign['funding_goal'] ?? 0),
                'amount_raised' => (float)($campaign['amount_raised'] ?? 0),
                'deadline' => $campaign['deadline'] ?? null,
                'farmer_name' => $campaign['farmer_name'] ?? 'Unknown',
                'status' => $campaign['status'] ?? 'active',
                'supporters' => (int)($campaign['supporters'] ?? 0)
            ];
        }, $campaigns);
        $this->json(['success' => true, 'campaigns' => $formattedCampaigns]);
    }

    public function create(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $data = [
                'title' => $input['title'] ?? $input['campaignTitle'] ?? '',
                'description' => $input['description'] ?? $input['campaignDescription'] ?? '',
                'campaign_type' => $input['campaign_type'] ?? $input['campaignType'] ?? $input['type'] ?? '',
                'funding_goal' => (float)($input['funding_goal'] ?? $input['fundingGoal'] ?? $input['campaignGoal'] ?? 0),
                'deadline' => $input['deadline'] ?? $input['campaignDeadline'] ?? null,
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


<?php
/**
 * Admin Controller
 * PHP 8 Compatible
 */

class AdminController extends Controller {
    private User $userModel;
    private Farmer $farmerModel;
    private Campaign $campaignModel;

    public function __construct() {
        parent::__construct();
        require_once MODELS_PATH . '/User.php';
        require_once MODELS_PATH . '/Farmer.php';
        require_once MODELS_PATH . '/Campaign.php';
        $this->userModel = new User();
        $this->farmerModel = new Farmer();
        $this->campaignModel = new Campaign();
    }

    public function users(): void {
        $this->requireRole('admin');
        
        $users = $this->userModel->getAll(50);
        $stats = [
            'total_users' => count($users),
            'admin_count' => count(array_filter($users, fn($u) => $u['role'] === 'admin')),
            'farmer_count' => count(array_filter($users, fn($u) => $u['role'] === 'farmer')),
            'active_count' => count(array_filter($users, fn($u) => $u['status'] === 'active'))
        ];

        $this->view('admin/users', [
            'users' => $users,
            'stats' => $stats
        ]);
    }

    public function farmers(): void {
        $this->requireRole('admin');
        
        $farmers = $this->farmerModel->getAll(50);
        $stats = [
            'total_farmers' => count($farmers),
            'avg_experience' => !empty($farmers) ? round(array_sum(array_column($farmers, 'years_experience')) / count($farmers), 1) : 0,
            'total_farm_size' => array_sum(array_column($farmers, 'farm_size'))
        ];

        $this->view('admin/farmers', [
            'farmers' => $farmers,
            'stats' => $stats
        ]);
    }

    public function campaigns(): void {
        $this->requireRole('admin');
        
        $campaigns = $this->campaignModel->getAll(50);
        $stats = [
            'total_campaigns' => count($campaigns),
            'active_campaigns' => $this->campaignModel->countActive(),
            'total_funding_goal' => array_sum(array_column($campaigns, 'funding_goal')),
            'total_raised' => array_sum(array_column($campaigns, 'amount_raised'))
        ];

        $this->view('admin/campaigns', [
            'campaigns' => $campaigns,
            'stats' => $stats
        ]);
    }
}

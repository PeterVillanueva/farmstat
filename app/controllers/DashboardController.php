<?php
/**
 * Dashboard Controller
 * PHP 8 Compatible
 */

class DashboardController extends Controller {
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

    public function index(): void {
        $this->requireAuth();
        
        if ($_SESSION['user_role'] === 'admin') {
            $this->admin();
        } else {
            $this->farmer();
        }
    }

    public function admin(): void {
        $this->requireRole('admin');

        $stats = [
            'total_users' => count($this->userModel->getAll()),
            'total_farmers' => $this->farmerModel->count(),
            'total_funding' => $this->campaignModel->getTotalFunding(),
            'active_campaigns' => $this->campaignModel->countActive()
        ];

        $this->view('dashboard/admin', ['stats' => $stats]);
    }

    public function farmer(): void {
        $this->requireAuth();

        $user = $this->userModel->findById($_SESSION['user_id']);
        $campaigns = $this->campaignModel->getAll(10);
        $farmers = $this->farmerModel->getAll(10);

        $this->view('dashboard/farmer', [
            'user' => $user,
            'campaigns' => $campaigns,
            'farmers' => $farmers
        ]);
    }
}


<?php
/**
 * Home Controller
 * PHP 8 Compatible
 */

class HomeController extends Controller {
    public function index(): void {
        $this->view('home/index');
    }
}


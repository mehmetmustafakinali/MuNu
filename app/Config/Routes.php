<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================================================
// MUNU - Müşteri Takip ve Ön Muhasebe Sistemi
// Route Tanımlamaları
// =====================================================

// Ana Sayfa
$routes->get('/', 'Munu::index');
$routes->get('munu', 'Munu::index');
$routes->get('Munu', 'Munu::index');

// -----------------------------------------------------
// MÜŞTERİ İŞLEMLERİ
// -----------------------------------------------------
$routes->post('Munu/add_customer', 'Munu::add_customer');
$routes->post('Munu/update_customer/(:num)', 'Munu::update_customer/$1');
$routes->get('Munu/delete_customer/(:num)', 'Munu::delete_customer/$1');
$routes->get('Munu/get_customer/(:num)', 'Munu::get_customer/$1');
$routes->get('Munu/customer_report/(:num)', 'Munu::customer_report/$1');
$routes->get('Munu/search_customer', 'Munu::search_customer');

// -----------------------------------------------------
// İŞLEM (BORÇ/TAHSİLAT) İŞLEMLERİ
// -----------------------------------------------------
$routes->post('Munu/add_transaction', 'Munu::add_transaction');
$routes->get('Munu/delete_transaction/(:num)', 'Munu::delete_transaction/$1');

// -----------------------------------------------------
// KARALAMA DEFTERİ / NOT İŞLEMLERİ
// -----------------------------------------------------
$routes->post('Munu/add_note', 'Munu::add_note');
$routes->post('Munu/update_note/(:num)', 'Munu::update_note/$1');
$routes->get('Munu/complete_note/(:num)', 'Munu::complete_note/$1');
$routes->get('Munu/toggle_pin_note/(:num)', 'Munu::toggle_pin_note/$1');
$routes->get('Munu/delete_note/(:num)', 'Munu::delete_note/$1');
$routes->get('Munu/get_note/(:num)', 'Munu::get_note/$1');
$routes->get('Munu/get_calendar_data', 'Munu::get_calendar_data');

// -----------------------------------------------------
// BENİM BORÇLARIM İŞLEMLERİ
// -----------------------------------------------------
$routes->post('Munu/add_my_debt', 'Munu::add_my_debt');
$routes->get('Munu/pay_my_debt/(:num)', 'Munu::pay_my_debt/$1');
$routes->get('Munu/delete_my_debt/(:num)', 'Munu::delete_my_debt/$1');

// ALACAKLI YÖNETİMİ
$routes->post('Munu/add_creditor', 'Munu::add_creditor');
$routes->post('Munu/update_creditor/(:num)', 'Munu::update_creditor/$1');
$routes->get('Munu/delete_creditor/(:num)', 'Munu::delete_creditor/$1');
$routes->get('Munu/creditor_report/(:num)', 'Munu::creditor_report/$1');
$routes->get('Munu/get_creditor/(:num)', 'Munu::get_creditor/$1');

// -----------------------------------------------------
// KULLANICI İŞLEMLERİ
// -----------------------------------------------------
$routes->post('Munu/add_user', 'Munu::add_user');
$routes->get('Munu/delete_user/(:num)', 'Munu::delete_user/$1');
$routes->post('Munu/change_password', 'Munu::change_password');

// -----------------------------------------------------
// AJAX / API İŞLEMLERİ
// -----------------------------------------------------
$routes->get('Munu/get_dashboard_data', 'Munu::get_dashboard_data');
$routes->get('Munu/get_monthly_report', 'Munu::get_monthly_report');

// -----------------------------------------------------
// AI ASISTAN
// -----------------------------------------------------
$routes->post('Munu/ai_chat', 'AiController::chat');

// -----------------------------------------------------
// KİMLİK DOĞRULAMA (AUTH)
// -----------------------------------------------------
$routes->get('Auth/login', 'Auth::login');
$routes->post('Auth/login', 'Auth::login');
$routes->post('Auth/register_company', 'Auth::register_company');
$routes->get('Auth/logout', 'Auth::logout');


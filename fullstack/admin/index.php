<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /house_rental/login.php');
    exit();
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /house_rental/index.php');
    exit();
}

// Get user info
$admin_name = $_SESSION['full_name'] ?? 'Admin';
$admin_email = $_SESSION['email'] ?? '';
$admin_username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AmarThikana</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #1abc9c;
            --accent-color: #3498db;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --success-color: #27ae60;
            --info-color: #3498db;
            --background-light: #f5f7fa;
            --background-white: #ffffff;
            --text-dark: #34495e;
            --text-medium: #7f8c8d;
            --border-color: #e0e6ed;
            --shadow-soft: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 15px rgba(0,0,0,0.1);
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Admin Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 10px;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            color: #ecf0f1;
        }

        .admin-nav {
            list-style: none;
            padding: 20px 0;
        }

        .admin-nav li {
            margin-bottom: 5px;
        }

        .admin-nav li a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .admin-nav li a:hover,
        .admin-nav li a.active {
            background: rgba(26, 188, 156, 0.2);
            border-left: 4px solid var(--secondary-color);
            padding-left: 21px;
        }

        .admin-nav li a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger-color);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Main Content */
        .admin-content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
            transition: all 0.3s ease;
        }

        /* Header */
        .content-header {
            background: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .header-title p {
            color: var(--text-medium);
            font-size: 0.95rem;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            background: var(--background-light);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: var(--border-color);
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--secondary-color);
        }

        /* Animated Admin Icon */
        .admin-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(26, 188, 156, 0.3);
            animation: pulse 2s infinite;
            position: relative;
        }

        .admin-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid var(--secondary-color);
            animation: ripple 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        .user-profile .user-info {
            text-align: right;
        }

        .user-profile .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
            display: block;
        }

        .user-profile .user-role {
            font-size: 0.75rem;
            color: var(--text-medium);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .stat-card.primary::before { background: var(--secondary-color); }
        .stat-card.warning::before { background: var(--warning-color); }
        .stat-card.danger::before { background: var(--danger-color); }
        .stat-card.info::before { background: var(--info-color); }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-icon.primary {
            background: rgba(26, 188, 156, 0.1);
            color: var(--secondary-color);
        }

        .stat-icon.warning {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }

        .stat-icon.danger {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }

        .stat-icon.info {
            background: rgba(52, 152, 219, 0.1);
            color: var(--info-color);
        }

        .stat-details {
            flex: 1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.95rem;
            color: var(--text-medium);
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.85rem;
            margin-top: 5px;
            font-weight: 600;
        }

        .stat-change.positive {
            color: var(--success-color);
        }

        .stat-change.negative {
            color: var(--danger-color);
        }

        /* Content Section */
        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
        }

        .data-table thead th {
            text-align: left;
            background: var(--background-light);
            padding: 15px 20px;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tbody td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
        }

        .data-table tbody tr:hover {
            background: var(--background-light);
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border-color);
        }

        .user-name-cell {
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--text-medium);
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge.active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge.inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.landlord {
            background: rgba(26, 188, 156, 0.1);
            color: var(--secondary-color);
        }

        .badge.tenant {
            background: rgba(52, 152, 219, 0.1);
            color: var(--info-color);
        }

        .badge.admin {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #16a085;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-warning {
            background: var(--warning-color);
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
        }

        .btn-info {
            background: var(--info-color);
            color: white;
        }

        .btn-info:hover {
            background: #2980b9;
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 0.85rem;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Tooltip styles */
        .tooltip-wrapper {
            position: relative;
            display: inline-block;
        }

        .tooltip-text {
            visibility: hidden;
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 6px 12px;
            border-radius: 6px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s, visibility 0.3s;
            font-size: 0.8rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: #2c3e50 transparent transparent transparent;
        }

        .tooltip-wrapper:hover .tooltip-text,
        .tooltip-wrapper:focus .tooltip-text,
        .tooltip-wrapper:active .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Mobile tooltip adjustment */
        @media (max-width: 768px) {
            .tooltip-text {
                bottom: auto;
                top: 125%;
            }

            .tooltip-text::after {
                top: auto;
                bottom: 100%;
                border-color: transparent transparent #2c3e50 transparent;
            }
        }

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .chart-placeholder {
            height: 250px;
            background: var(--background-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-medium);
            font-size: 0.9rem;
        }

        /* Recent Activity */
        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 15px;
            border-left: 3px solid var(--border-color);
            margin-bottom: 15px;
            background: var(--background-light);
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            border-left-color: var(--secondary-color);
            background: white;
            box-shadow: var(--shadow-soft);
        }

        .activity-item.new::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background: var(--secondary-color);
            border-radius: 50%;
            border: 3px solid white;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .activity-title {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .activity-time {
            font-size: 0.8rem;
            color: var(--text-medium);
        }

        .activity-desc {
            font-size: 0.9rem;
            color: var(--text-medium);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.1);
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: var(--border-radius);
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 25px 30px;
            border-bottom: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .modal-close {
            background: transparent;
            border: none;
            font-size: 1.8rem;
            color: var(--text-medium);
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: var(--border-color);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
        }

        .modal-footer {
            padding: 20px 30px;
            border-top: 2px solid var(--border-color);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        /* Settings Tabs */
        .settings-tab {
            display: none;
        }

        .settings-tab.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .admin-sidebar.mobile-active {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
            }

            .admin-content {
                margin-left: 0;
            }

            /* Mobile Menu Toggle Button */
            .mobile-menu-toggle {
                display: block;
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
                border-radius: 50%;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(26, 188, 156, 0.4);
                z-index: 999;
                transition: all 0.3s ease;
            }

            .mobile-menu-toggle:hover {
                transform: scale(1.1);
            }

            .mobile-menu-toggle:active {
                transform: scale(0.95);
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .stat-value {
                font-size: 1.8rem;
            }

            .content-header {
                padding: 20px;
            }

            .header-title h1 {
                font-size: 1.6rem;
            }

            .user-profile .user-info {
                display: none;
            }

            .table-container {
                overflow-x: auto;
            }

            .data-table {
                min-width: 800px;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .table-actions {
                width: 100%;
                flex-direction: column;
                gap: 10px;
            }

            .table-actions input,
            .table-actions select {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .admin-content {
                padding: 20px 15px;
            }

            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                padding: 15px;
            }

            .header-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .stat-card {
                padding: 18px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .chart-card {
                padding: 20px;
            }

            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .chart-header select {
                width: 100%;
            }

            .modal {
                width: 95%;
                max-height: 95vh;
            }

            .modal-header,
            .modal-body,
            .modal-footer {
                padding: 20px 15px;
            }

            .modal-header h3 {
                font-size: 1.3rem;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
            }

            .action-btns {
                flex-wrap: wrap;
            }

            .user-cell {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .data-table thead th,
            .data-table tbody td {
                padding: 12px 10px;
                font-size: 0.85rem;
            }

            .badge {
                font-size: 0.7rem;
                padding: 4px 10px;
            }

            .btn-sm {
                padding: 5px 10px;
                font-size: 0.8rem;
            }

            .action-btns {
                gap: 5px;
            }

            .action-btns .btn {
                flex: 1;
                min-width: 40px;
                justify-content: center;
            }

            /* Make action buttons icon-only on very small screens */
            .action-btns .btn i {
                margin: 0;
            }

            /* Settings responsive */
            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="tel"],
            .form-group input[type="number"],
            .form-group input[type="password"],
            .form-group select,
            .form-group textarea {
                font-size: 16px; /* Prevents zoom on iOS */
            }
        }

        @media (max-width: 576px) {
            .header-title h1 {
                font-size: 1.4rem;
            }

            .header-title p {
                font-size: 0.85rem;
            }

            .admin-icon {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }

            .stat-value {
                font-size: 1.6rem;
            }

            .stat-label {
                font-size: 0.85rem;
            }

            .stat-change {
                font-size: 0.75rem;
            }

            .table-header h3 {
                font-size: 1.1rem;
            }

            .btn {
                padding: 10px 16px;
                font-size: 0.85rem;
            }

            .chart-placeholder {
                height: 200px;
            }

            .activity-item {
                padding: 12px;
            }

            .activity-title {
                font-size: 0.9rem;
            }

            .activity-time {
                font-size: 0.75rem;
            }

            .activity-desc {
                font-size: 0.85rem;
            }

            /* Modal adjustments */
            .modal {
                width: 100%;
                height: 100vh;
                max-height: 100vh;
                border-radius: 0;
            }

            .modal-header h3 {
                font-size: 1.1rem;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            /* Settings grid responsive */
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-details p {
                font-size: 0.8rem;
            }

            /* Compact action buttons for small screens */
            .action-btns {
                gap: 4px;
                justify-content: flex-start;
            }

            .action-btns .btn-sm {
                padding: 6px 8px;
                font-size: 0.75rem;
                min-width: 36px;
            }

            .data-table tbody td {
                font-size: 0.8rem;
                padding: 10px 8px;
            }

            .data-table thead th {
                font-size: 0.75rem;
                padding: 10px 8px;
            }

            /* Stack action buttons vertically on very small screens if needed */
            @media (max-width: 400px) {
                .action-btns {
                    flex-direction: column;
                    gap: 5px;
                }

                .action-btns .btn {
                    width: 100%;
                }
            }
        }

        /* Mobile Menu Toggle (hidden by default) */
        .mobile-menu-toggle {
            display: none;
        }

        /* Overlay for mobile menu */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }

        .sidebar-overlay.active {
            display: block;
        }

        @media (max-width: 992px) {
            .mobile-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <h2>AmarThikana</h2>
                <p>Admin Panel</p>
            </div>
            <ul class="admin-nav">
                <li><a href="#" class="nav-link active" data-section="dashboard">
                    <i class="fas fa-th-large"></i> Dashboard
                </a></li>
                <li><a href="#" class="nav-link" data-section="users">
                    <i class="fas fa-users"></i> Users Management
                    <span class="nav-badge">156</span>
                </a></li>
                <li><a href="#" class="nav-link" data-section="properties">
                    <i class="fas fa-building"></i> Properties
                    <span class="nav-badge">42</span>
                </a></li>
                <li><a href="#" class="nav-link" data-section="bookings">
                    <i class="fas fa-calendar-check"></i> Bookings
                    <span class="nav-badge">28</span>
                </a></li>
                <li><a href="#" class="nav-link" data-section="reviews">
                    <i class="fas fa-star"></i> Reviews
                </a></li>
                <li><a href="#" class="nav-link" data-section="reports">
                    <i class="fas fa-chart-line"></i> Reports & Analytics
                </a></li>
                <li><a href="#" class="nav-link" data-section="settings">
                    <i class="fas fa-cog"></i> Settings
                </a></li>
                <li><a href="../api/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <div class="content-header">
                    <div class="header-title">
                        <h1>Dashboard Overview</h1>
                        <p>Welcome back, <?php echo htmlspecialchars($admin_name); ?>! Here's what's happening today.</p>
                    </div>
                    <div class="header-actions">
                        <div class="user-profile">
                            <div class="admin-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($admin_name); ?></span>
                                <span class="user-role">Administrator</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">156</div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 12% this month
                            </div>
                        </div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-icon warning">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">42</div>
                            <div class="stat-label">Active Properties</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 8 new this week
                            </div>
                        </div>
                    </div>

                    <div class="stat-card info">
                        <div class="stat-icon info">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">28</div>
                            <div class="stat-label">Active Bookings</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 15% increase
                            </div>
                        </div>
                    </div>

                    <div class="stat-card danger">
                        <div class="stat-icon danger">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-value">৳45,680</div>
                            <div class="stat-label">Total Revenue</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> ৳5,200 this week
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Revenue Overview</h3>
                            <select class="form-control" style="width: auto; padding: 8px 12px;">
                                <option>Last 7 Days</option>
                                <option>Last 30 Days</option>
                                <option>Last 3 Months</option>
                            </select>
                        </div>
                        <div class="chart-placeholder">
                            <i class="fas fa-chart-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Property Statistics</h3>
                        </div>
                        <div class="chart-placeholder">
                            <i class="fas fa-chart-pie" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Recent Activity</h3>
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                    <ul class="activity-list" style="padding: 20px;">
                        <li class="activity-item new" style="position: relative;">
                            <div class="activity-header">
                                <span class="activity-title">New Property Listed</span>
                                <span class="activity-time">2 minutes ago</span>
                            </div>
                            <p class="activity-desc">Modern Apartment in Gulshan by John Doe</p>
                        </li>
                        <li class="activity-item">
                            <div class="activity-header">
                                <span class="activity-title">Booking Confirmed</span>
                                <span class="activity-time">15 minutes ago</span>
                            </div>
                            <p class="activity-desc">Sarah Johnson booked Luxury Villa for 5 nights</p>
                        </li>
                        <li class="activity-item">
                            <div class="activity-header">
                                <span class="activity-title">New User Registered</span>
                                <span class="activity-time">1 hour ago</span>
                            </div>
                            <p class="activity-desc">Michael Brown joined as a Landlord</p>
                        </li>
                        <li class="activity-item">
                            <div class="activity-header">
                                <span class="activity-title">Payment Received</span>
                                <span class="activity-time">2 hours ago</span>
                            </div>
                            <p class="activity-desc">৳8,500 received from Emma Wilson</p>
                        </li>
                        <li class="activity-item">
                            <div class="activity-header">
                                <span class="activity-title">New Review Posted</span>
                                <span class="activity-time">3 hours ago</span>
                            </div>
                            <p class="activity-desc">5-star review on Beachfront Villa by David Lee</p>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Users Management Section -->
            <section id="users" class="content-section">
                <div class="content-header">
                    <div class="header-title">
                        <h1>Users Management</h1>
                        <p>Manage all registered users and their permissions</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="openAddUserModal()">
                            <i class="fas fa-plus"></i> Add New User
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3>All Users (<span id="userCount">0</span>)</h3>
                        <div class="table-actions">
                            <input type="text" id="userSearch" class="form-control" placeholder="Search users..." style="width: 250px; padding: 8px 12px;">
                            <select id="roleFilter" class="form-control" style="width: auto; padding: 8px 12px;">
                                <option value="all">All Roles</option>
                                <option value="tenant">Tenants</option>
                                <option value="landlord">Landlords</option>
                                <option value="admin">Admins</option>
                            </select>
                            <select id="statusFilter" class="form-control" style="width: auto; padding: 8px 12px;">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <button class="btn btn-info btn-sm" onclick="loadUsers()">
                                <i class="fas fa-sync"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--secondary-color);"></i>
                                    <p style="margin-top: 10px; color: var(--text-medium);">Loading users...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div id="paginationContainer" style="display: none; padding: 20px; text-align: center;">
                        <div id="paginationInfo" style="margin-bottom: 10px; color: var(--text-medium);"></div>
                        <div id="paginationButtons"></div>
                    </div>
                </div>
            </section>

            <!-- Properties Section -->
            <section id="properties" class="content-section">
                <div class="content-header">
                    <div class="header-title">
                        <h1>Properties Management</h1>
                        <p>Manage all listed properties</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3>All Properties (42)</h3>
                        <div class="table-actions">
                            <input type="text" class="form-control" placeholder="Search properties..." style="width: 250px; padding: 8px 12px;">
                            <select class="form-control" style="width: auto; padding: 8px 12px;">
                                <option>All Status</option>
                                <option>Active</option>
                                <option>Pending</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Property Name</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Price/Night</th>
                                <th>Status</th>
                                <th>Listed Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="user-name-cell">Cozy Apartment in Downtown</td>
                                <td>Sophia Martinez</td>
                                <td>Apartment</td>
                                <td>৳150</td>
                                <td><span class="badge active">Active</span></td>
                                <td>Jan 20, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Property</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <span class="tooltip-text">Edit Property</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Delete Property</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="user-name-cell">Luxury Villa by the Beach</td>
                                <td>Noah Foster</td>
                                <td>Villa</td>
                                <td>৳350</td>
                                <td><span class="badge active">Active</span></td>
                                <td>Feb 5, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Property</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <span class="tooltip-text">Edit Property</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Delete Property</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="user-name-cell">Modern Studio in Gulshan</td>
                                <td>Sophia Martinez</td>
                                <td>Studio</td>
                                <td>৳120</td>
                                <td><span class="badge pending">Pending</span></td>
                                <td>Mar 15, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Bookings Section -->
            <section id="bookings" class="content-section">
                <div class="content-header">
                    <div class="header-title">
                        <h1>Bookings Management</h1>
                        <p>Track and manage all property bookings</p>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3>Recent Bookings (28)</h3>
                        <div class="table-actions">
                            <select class="form-control" style="width: auto; padding: 8px 12px;">
                                <option>All Status</option>
                                <option>Confirmed</option>
                                <option>Pending</option>
                                <option>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Property</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=100" alt="User" class="user-avatar">
                                        <span class="user-name-cell">Liam Harper</span>
                                    </div>
                                </td>
                                <td>Cozy Apartment</td>
                                <td>Oct 15, 2025</td>
                                <td>Oct 20, 2025</td>
                                <td>৳750</td>
                                <td><span class="badge active">Confirmed</span></td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Booking</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <span class="tooltip-text">Cancel Booking</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://images.pexels.com/photos/1065084/pexels-photo-1065084.jpeg?auto=compress&cs=tinysrgb&w=100" alt="User" class="user-avatar">
                                        <span class="user-name-cell">Ava Bennett</span>
                                    </div>
                                </td>
                                <td>Luxury Villa</td>
                                <td>Oct 25, 2025</td>
                                <td>Nov 1, 2025</td>
                                <td>৳2,450</td>
                                <td><span class="badge pending">Pending</span></td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Booking</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <span class="tooltip-text">Cancel Booking</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Reviews Section -->
            <section id="reviews" class="content-section">
                <div class="content-header">
                    <div class="header-title">
                        <h1>Reviews Management</h1>
                        <p>Monitor and moderate user reviews</p>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3>All Reviews</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reviewer</th>
                                <th>Property</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=100" alt="User" class="user-avatar">
                                        <span class="user-name-cell">Emma Wilson</span>
                                    </div>
                                </td>
                                <td>Cozy Apartment</td>
                                <td>
                                    <div style="color: #f1c40f;">
                                        ⭐⭐⭐⭐⭐
                                    </div>
                                </td>
                                <td>Amazing place! Very clean and comfortable.</td>
                                <td>Oct 8, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Review</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Delete Review</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports" class="content-section">
                <div class="content-header">
                    <div class="header-title">
                        <h1>Reports & Analytics</h1>
                        <p>View detailed analytics and generate reports</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Generate Report
                        </button>
                    </div>
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>User Growth</h3>
                        </div>
                        <div class="chart-placeholder">
                            <i class="fas fa-chart-area" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Booking Trends</h3>
                        </div>
                        <div class="chart-placeholder">
                            <i class="fas fa-chart-bar" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Revenue by Property Type</h3>
                        </div>
                        <div class="chart-placeholder">
                            <i class="fas fa-chart-pie" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Monthly Performance</h3>
                        </div>
                        <div class="chart-placeholder">
                            <i class="fas fa-chart-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Section -->
            <section id="settings" class="content-section">
                <div class="content-header">
                    <div class="header-title">
                        <h1>System Settings</h1>
                        <p>Configure and manage your system preferences</p>
                    </div>
                </div>

                <!-- Settings Grid -->
                <div class="stats-grid" style="margin-bottom: 30px;">
                    <div class="stat-card primary clickable active" onclick="showSettingsTab('general', this)">
                        <div class="stat-icon primary">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label" style="font-size: 1.1rem; font-weight: 600;">General Settings</div>
                            <p style="font-size: 0.85rem; color: var(--text-medium); margin-top: 5px;">Site configuration & branding</p>
                        </div>
                    </div>

                    <div class="stat-card info clickable" onclick="showSettingsTab('security', this)">
                        <div class="stat-icon info">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label" style="font-size: 1.1rem; font-weight: 600;">Security Settings</div>
                            <p style="font-size: 0.85rem; color: var(--text-medium); margin-top: 5px;">Authentication & access control</p>
                        </div>
                    </div>

                    <div class="stat-card warning clickable" onclick="showSettingsTab('notifications', this)">
                        <div class="stat-icon warning">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label" style="font-size: 1.1rem; font-weight: 600;">Notifications</div>
                            <p style="font-size: 0.85rem; color: var(--text-medium); margin-top: 5px;">Email & SMS alerts</p>
                        </div>
                    </div>
                </div>

                <!-- General Settings Tab -->
                <div id="generalSettings" class="settings-tab active">
                    <div class="table-container">
                        <div class="table-header">
                            <h3><i class="fas fa-globe"></i> General Settings</h3>
                        </div>
                        <div style="padding: 30px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-envelope"></i> Admin Email</label>
                                    <input type="email" class="form-control" id="adminEmail" value="admin@amarthikana.com" placeholder="admin@example.com">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-phone"></i> Contact Phone</label>
                                    <input type="tel" class="form-control" id="contactPhone" value="+880 1234-567890" placeholder="+880 XXXX-XXXXXX">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marker-alt"></i> Business Address</label>
                                    <input type="text" class="form-control" id="businessAddress" value="Dhaka, Bangladesh" placeholder="Enter address">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-globe-asia"></i> Timezone</label>
                                    <select class="form-control" id="timezone">
                                        <option>Asia/Dhaka (GMT+6)</option>
                                        <option>Asia/Kolkata (GMT+5:30)</option>
                                        <option>UTC (GMT+0)</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-language"></i> Default Language</label>
                                    <select class="form-control" id="defaultLanguage">
                                        <option>English</option>
                                        <option>বাংলা (Bangla)</option>
                                        <option>Hindi</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-money-bill-wave"></i> Currency</label>
                                    <select class="form-control" id="currency">
                                        <option>BDT (৳)</option>
                                        <option>USD ($)</option>
                                        <option>EUR (€)</option>
                                        <option>INR (₹)</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" id="saveGeneralSettings" onclick="saveGeneralSettings()" style="margin-top: 10px;">
                                <i class="fas fa-save"></i> Save General Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Security Settings Tab -->
                <div id="securitySettings" class="settings-tab">
                    <div class="table-container">
                        <div class="table-header">
                            <h3><i class="fas fa-shield-alt"></i> Security & Access Control</h3>
                        </div>
                        <div style="padding: 30px;">
                            <h4 style="margin-bottom: 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-lock"></i> Password & Authentication
                            </h4>
                            <div class="form-group">
                                <label><i class="fas fa-key"></i> Change Admin Password</label>
                                <button class="btn btn-warning" onclick="openChangePasswordModal()" style="margin-top: 10px;">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user-check"></i> Account Verification Method</label>
                                <select class="form-control" id="verificationMethod">
                                    <option>Email Verification Required</option>
                                    <option>Phone Verification Required</option>
                                    <option>Both Email & Phone Required</option>
                                    <option>Manual Admin Approval</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-key"></i> Minimum Password Length</label>
                                <input type="number" class="form-control" id="minPasswordLength" value="8" min="6" max="20">
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="requireSpecialChars" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-spell-check"></i> Require special characters in password</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="requireNumbers" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-sort-numeric-up"></i> Require numbers in password</span>
                                </label>
                            </div>

                            <h4 style="margin: 30px 0 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-shield-virus"></i> Security Features
                            </h4>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="enableIpTracking" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-fingerprint"></i> Enable IP Tracking & Logging</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="sendSecurityAlerts" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-exclamation-triangle"></i> Send Security Alerts via Email</span>
                                </label>
                            </div>
                            <button class="btn btn-primary" id="saveSecuritySettings" onclick="saveSecuritySettings()">
                                <i class="fas fa-save"></i> Save Security Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notifications Settings Tab -->
                <div id="notificationsSettings" class="settings-tab">
                    <div class="table-container">
                        <div class="table-header">
                            <h3><i class="fas fa-bell"></i> Notification Preferences</h3>
                        </div>
                        <div style="padding: 30px;">
                            <h4 style="margin-bottom: 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-envelope"></i> Email Notifications
                            </h4>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="emailNewUser" style="width: auto; margin-right: 10px;" checked>
                                    <span>New user registration</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="emailNewProperty" style="width: auto; margin-right: 10px;" checked>
                                    <span>New property listing</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="emailBookingConfirm" style="width: auto; margin-right: 10px;" checked>
                                    <span>Booking confirmations</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="emailPaymentTrans" style="width: auto; margin-right: 10px;" checked>
                                    <span>Payment transactions</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="emailSystemUpdates" style="width: auto; margin-right: 10px;">
                                    <span>System updates</span>
                                </label>
                            </div>

                            <h4 style="margin: 30px 0 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-sms"></i> SMS Notifications
                            </h4>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="smsCriticalAlerts" style="width: auto; margin-right: 10px;" checked>
                                    <span>Critical alerts only</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="smsBookingNotif" style="width: auto; margin-right: 10px;">
                                    <span>Booking notifications</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" id="smsPaymentConfirm" style="width: auto; margin-right: 10px;">
                                    <span>Payment confirmations</span>
                                </label>
                            </div>

                            <h4 style="margin: 30px 0 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-cog"></i> Notification Settings
                            </h4>
                            <div class="form-group">
                                <label><i class="fas fa-server"></i> SMTP Server</label>
                                <input type="text" class="form-control" id="smtpServer" value="smtp.gmail.com" placeholder="smtp.example.com">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> SMTP Username</label>
                                    <input type="text" class="form-control" id="smtpUsername" value="noreply@amarthikana.com">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-lock"></i> SMTP Password</label>
                                    <input type="password" class="form-control" id="smtpPassword" value="••••••••">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-plug"></i> SMTP Port</label>
                                    <input type="number" class="form-control" id="smtpPort" value="587">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-shield-alt"></i> Encryption</label>
                                    <select class="form-control" id="smtpEncryption">
                                        <option>TLS</option>
                                        <option>SSL</option>
                                        <option>None</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" id="saveNotificationSettings" onclick="saveNotificationSettings()">
                                <i class="fas fa-save"></i> Save Notification Settings
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Mobile Menu Toggle Button -->
        <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Add New User</h3>
                <button class="modal-close" onclick="closeAddUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" class="form-control" id="addFullName" required placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-at"></i> Username *</label>
                        <input type="text" class="form-control" id="addUsername" required placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" class="form-control" id="addEmail" required placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" class="form-control" id="addPhone" placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> User Role *</label>
                        <select class="form-control" id="addRole" required>
                            <option value="">Select Role</option>
                            <option value="tenant">Tenant</option>
                            <option value="landlord">Landlord</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password *</label>
                        <input type="password" class="form-control" id="addPassword" required placeholder="Enter password" minlength="6">
                        <small style="color: var(--text-medium); font-size: 0.85rem;">
                            Password must be at least 6 characters long
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeAddUserModal()">Cancel</button>
                <button class="btn btn-primary" id="addUserBtn" onclick="submitAddUser()">
                    <i class="fas fa-save"></i> Add User
                </button>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i> Edit User</h3>
                <button class="modal-close" onclick="closeEditUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" class="form-control" id="editFullName" required placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-at"></i> Username *</label>
                        <input type="text" class="form-control" id="editUsername" required placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" class="form-control" id="editEmail" required placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" class="form-control" id="editPhone" placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> User Role *</label>
                        <select class="form-control" id="editRole" required>
                            <option value="">Select Role</option>
                            <option value="tenant">Tenant</option>
                            <option value="landlord">Landlord</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-toggle-on"></i> Account Status *</label>
                        <select class="form-control" id="editStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" id="changePassword" style="width: auto; margin-right: 10px;">
                            <span><i class="fas fa-key"></i> Change Password</span>
                        </label>
                    </div>
                    <div class="form-group" id="newPasswordGroup" style="display: none;">
                        <label><i class="fas fa-lock"></i> New Password *</label>
                        <input type="password" class="form-control" id="editNewPassword" placeholder="Enter new password" minlength="6">
                        <small style="color: var(--text-medium); font-size: 0.85rem;">
                            Password must be at least 6 characters long
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeEditUserModal()">Cancel</button>
                <button class="btn btn-primary" id="editUserBtn" onclick="submitEditUser()">
                    <i class="fas fa-save"></i> Update User
                </button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-key"></i> Change Admin Password</h3>
                <button class="modal-close" onclick="closeChangePasswordModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Current Password *</label>
                        <input type="password" class="form-control" id="currentPassword" required placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> New Password *</label>
                        <input type="password" class="form-control" id="newPassword" required placeholder="Enter new password" minlength="8">
                        <small style="color: var(--text-medium); font-size: 0.85rem;">
                            Password must be at least 8 characters long
                        </small>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-check-circle"></i> Confirm New Password *</label>
                        <input type="password" class="form-control" id="confirmPassword" required placeholder="Re-enter new password">
                    </div>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin-top: 20px;">
                        <strong><i class="fas fa-info-circle"></i> Password Requirements:</strong>
                        <ul style="margin: 10px 0 0 20px; font-size: 0.9rem;">
                            <li>Minimum 8 characters</li>
                            <li>At least one number</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeChangePasswordModal()">Cancel</button>
                <button class="btn btn-primary" id="changePasswordBtn" onclick="submitChangePassword()">
                    <i class="fas fa-save"></i> Change Password
                </button>
            </div>
        </div>
    </div>

    <script>
        // Mobile Sidebar Toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('mobile-active');
            overlay.classList.toggle('active');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
        }

        // Close sidebar when clicking on a nav link (mobile)
        document.querySelectorAll('.admin-nav a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeMobileSidebar();
                }
            });
        });

        // Navigation
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.content-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetSection = this.getAttribute('data-section');
                    if (!targetSection) return;

                    // Remove active class from all links and sections
                    navLinks.forEach(l => l.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));

                    // Add active class to clicked link and target section
                    this.classList.add('active');
                    document.getElementById(targetSection).classList.add('active');

                    // Load users when users section is activated
                    if (targetSection === 'users') {
                        loadUsers();
                    }
                });
            });

            // Load settings
            loadSettings();
        });

        // Global variables for user management
        let currentPage = 1;
        let currentSearch = '';
        let currentRoleFilter = 'all';
        let currentStatusFilter = 'all';

        // Load users function
        async function loadUsers(page = 1) {
            currentPage = page;
            const usersTableBody = document.getElementById('usersTableBody');
            const userCount = document.getElementById('userCount');
            const paginationContainer = document.getElementById('paginationContainer');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationButtons = document.getElementById('paginationButtons');

            // Show loading state
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--secondary-color);"></i>
                        <p style="margin-top: 10px; color: var(--text-medium);">Loading users...</p>
                    </td>
                </tr>
            `;

            try {
                const params = new URLSearchParams({
                    page: page,
                    limit: 10,
                    search: currentSearch,
                    role: currentRoleFilter,
                    status: currentStatusFilter
                });

                const response = await fetch(`../api/list_users.php?${params}`);
                const result = await response.json();

                if (result.success) {
                    // Update user count
                    userCount.textContent = result.pagination.total_count;

                    // Render users table
                    if (result.users.length === 0) {
                        usersTableBody.innerHTML = `
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-users" style="font-size: 48px; color: var(--text-medium); opacity: 0.5;"></i>
                                    <p style="margin-top: 15px; color: var(--text-medium); font-size: 1.1rem;">No users found</p>
                                    <p style="color: var(--text-medium);">Try adjusting your search or filters</p>
                                </td>
                            </tr>
                        `;
                    } else {
                        usersTableBody.innerHTML = result.users.map(user => `
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="${user.profile_image}" alt="User" class="user-avatar" onerror="this.src='https://via.placeholder.com/40x40/cccccc/666666?text=${user.full_name.charAt(0)}'">
                                        <div>
                                            <div class="user-name-cell">${user.full_name}</div>
                                            <div style="font-size: 0.8rem; color: var(--text-medium);">@${user.username}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>${user.email}</td>
                                <td><span class="badge ${user.role}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span></td>
                                <td><span class="badge ${user.status}">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span></td>
                                <td>${user.created_at}</td>
                                <td>${user.last_login}</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm" onclick="viewUser(${user.id})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Details</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-warning btn-sm" onclick="editUser(${user.id})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <span class="tooltip-text">Edit User</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Delete User</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    }

                    // Update pagination
                    if (result.pagination.total_pages > 1) {
                        paginationContainer.style.display = 'block';
                        paginationInfo.textContent = `Page ${result.pagination.current_page} of ${result.pagination.total_pages} (${result.pagination.total_count} total users)`;

                        let buttonsHtml = '';

                        // Previous button
                        if (result.pagination.current_page > 1) {
                            buttonsHtml += `<button class="btn btn-sm" onclick="loadUsers(${result.pagination.current_page - 1})"><i class="fas fa-chevron-left"></i> Previous</button>`;
                        }

                        // Page numbers
                        const startPage = Math.max(1, result.pagination.current_page - 2);
                        const endPage = Math.min(result.pagination.total_pages, result.pagination.current_page + 2);

                        for (let i = startPage; i <= endPage; i++) {
                            if (i === result.pagination.current_page) {
                                buttonsHtml += `<button class="btn btn-primary btn-sm" disabled>${i}</button>`;
                            } else {
                                buttonsHtml += `<button class="btn btn-sm" onclick="loadUsers(${i})">${i}</button>`;
                            }
                        }

                        // Next button
                        if (result.pagination.current_page < result.pagination.total_pages) {
                            buttonsHtml += `<button class="btn btn-sm" onclick="loadUsers(${result.pagination.current_page + 1})">Next <i class="fas fa-chevron-right"></i></button>`;
                        }

                        paginationButtons.innerHTML = buttonsHtml;
                    } else {
                        paginationContainer.style.display = 'none';
                    }
                } else {
                    usersTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--danger-color);"></i>
                                <p style="margin-top: 15px; color: var(--danger-color); font-size: 1.1rem;">Error loading users</p>
                                <p style="color: var(--text-medium);">${result.message}</p>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading users:', error);
                usersTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--danger-color);"></i>
                            <p style="margin-top: 15px; color: var(--danger-color); font-size: 1.1rem;">Connection Error</p>
                            <p style="color: var(--text-medium);">Unable to load users. Please try again.</p>
                        </td>
                    </tr>
                `;
            }
        }

        // Search and filter functionality
        document.getElementById('userSearch')?.addEventListener('input', function() {
            currentSearch = this.value.trim();
            loadUsers(1);
        });

        document.getElementById('roleFilter')?.addEventListener('change', function() {
            currentRoleFilter = this.value;
            loadUsers(1);
        });

        document.getElementById('statusFilter')?.addEventListener('change', function() {
            currentStatusFilter = this.value;
            loadUsers(1);
        });

        // Modal Functions
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.add('active');
            document.getElementById('addUserForm').reset();
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.remove('active');
            document.getElementById('addUserForm').reset();
        }

        function openEditUserModal() {
            document.getElementById('editUserModal').classList.add('active');
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.remove('active');
            document.getElementById('editUserForm').reset();
            document.getElementById('newPasswordGroup').style.display = 'none';
            document.getElementById('changePassword').checked = false;
        }

        async function submitAddUser() {
            const form = document.getElementById('addUserForm');
            const btn = document.getElementById('addUserBtn');

            // Basic validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Collect form data
            const formData = new FormData();
            formData.append('full_name', document.getElementById('addFullName').value.trim());
            formData.append('username', document.getElementById('addUsername').value.trim());
            formData.append('email', document.getElementById('addEmail').value.trim());
            formData.append('phone', document.getElementById('addPhone').value.trim());
            formData.append('role', document.getElementById('addRole').value);
            formData.append('password', document.getElementById('addPassword').value);

            // Disable button
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

            try {
                const response = await fetch('../api/add_user.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('User added successfully! ✓');
                    closeAddUserModal();
                    loadUsers(); // Refresh the users list
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while adding the user. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Add User';
            }
        }

        async function submitEditUser() {
            const form = document.getElementById('editUserForm');
            const btn = document.getElementById('editUserBtn');

            // Basic validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Collect form data
            const formData = new FormData();
            formData.append('user_id', document.getElementById('editUserId').value);
            formData.append('full_name', document.getElementById('editFullName').value.trim());
            formData.append('username', document.getElementById('editUsername').value.trim());
            formData.append('email', document.getElementById('editEmail').value.trim());
            formData.append('phone', document.getElementById('editPhone').value.trim());
            formData.append('role', document.getElementById('editRole').value);
            formData.append('status', document.getElementById('editStatus').value);

            const changePassword = document.getElementById('changePassword').checked;
            if (changePassword) {
                const newPassword = document.getElementById('editNewPassword').value;
                if (!newPassword || newPassword.length < 6) {
                    alert('New password must be at least 6 characters long');
                    return;
                }
                formData.append('change_password', '1');
                formData.append('new_password', newPassword);
            }

            // Disable button
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

            try {
                const response = await fetch('../api/edit_user.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('User updated successfully! ✓');
                    closeEditUserModal();
                    loadUsers(); // Refresh the users list
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating the user. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Update User';
            }
        }

        // User Management Functions
        function viewUser(userId) {
            alert('Viewing user details for User ID: ' + userId + '\n\nThis feature will show detailed user information in a future update.');
        }

        async function editUser(userId) {
            try {
                // Fetch user data
                const response = await fetch(`../api/list_users.php?page=1&limit=1&search=${userId}`);
                const result = await response.json();

                if (result.success && result.users.length > 0) {
                    const user = result.users[0];

                    // Populate edit form
                    document.getElementById('editUserId').value = user.id;
                    document.getElementById('editFullName').value = user.full_name;
                    document.getElementById('editUsername').value = user.username;
                    document.getElementById('editEmail').value = user.email;
                    document.getElementById('editPhone').value = user.phone || '';
                    document.getElementById('editRole').value = user.role;
                    document.getElementById('editStatus').value = user.status;

                    openEditUserModal();
                } else {
                    alert('Error: Could not load user data');
                }
            } catch (error) {
                console.error('Error loading user for edit:', error);
                alert('An error occurred while loading user data. Please try again.');
            }
        }

        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to deactivate this user? This action cannot be undone.')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('user_id', userId);

                const response = await fetch('../api/delete_user.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    loadUsers(); // Refresh the users list
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the user. Please try again.');
            }
        }

        // Password change toggle
        document.getElementById('changePassword')?.addEventListener('change', function() {
            const passwordGroup = document.getElementById('newPasswordGroup');
            const passwordInput = document.getElementById('editNewPassword');

            if (this.checked) {
                passwordGroup.style.display = 'block';
                passwordInput.required = true;
            } else {
                passwordGroup.style.display = 'none';
                passwordInput.required = false;
                passwordInput.value = '';
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddUserModal();
                closeEditUserModal();
                closeChangePasswordModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('addUserModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddUserModal();
            }
        });

        document.getElementById('editUserModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditUserModal();
            }
        });

        // Settings Tab Switching
        function showSettingsTab(tabName, clickedCard) {
            // Remove active class from all cards and tabs
            const cards = document.querySelectorAll('.stat-card.clickable');
            const tabs = document.querySelectorAll('.settings-tab');

            cards.forEach(card => card.classList.remove('active'));
            tabs.forEach(tab => tab.classList.remove('active'));

            // Add active class to clicked card
            if (clickedCard) {
                clickedCard.classList.add('active');
            }

            // Show selected tab
            const selectedTab = document.getElementById(tabName + 'Settings');
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
        }

        async function loadSettings() {
            try {
                const response = await fetch('../api/load_settings.php');
                const result = await response.json();

                if (result.success) {
                    const settings = result.settings;

                    // General settings
                    if (settings.admin_email) document.getElementById('adminEmail').value = settings.admin_email;
                    if (settings.contact_phone) document.getElementById('contactPhone').value = settings.contact_phone;
                    if (settings.business_address) document.getElementById('businessAddress').value = settings.business_address;
                    if (settings.timezone) document.getElementById('timezone').value = settings.timezone;
                    if (settings.default_language) document.getElementById('defaultLanguage').value = settings.default_language;
                    if (settings.currency) document.getElementById('currency').value = settings.currency;

                    // Security settings
                    if (settings.verification_method) document.getElementById('verificationMethod').value = settings.verification_method;
                    if (settings.min_password_length) document.getElementById('minPasswordLength').value = settings.min_password_length;
                    if (settings.require_special_chars) document.getElementById('requireSpecialChars').checked = settings.require_special_chars == '1';
                    if (settings.require_numbers) document.getElementById('requireNumbers').checked = settings.require_numbers == '1';
                    if (settings.enable_ip_tracking) document.getElementById('enableIpTracking').checked = settings.enable_ip_tracking == '1';
                    if (settings.send_security_alerts) document.getElementById('sendSecurityAlerts').checked = settings.send_security_alerts == '1';

                    // Notification settings
                    if (settings.email_new_user) document.getElementById('emailNewUser').checked = settings.email_new_user == '1';
                    if (settings.email_new_property) document.getElementById('emailNewProperty').checked = settings.email_new_property == '1';
                    if (settings.email_booking_confirm) document.getElementById('emailBookingConfirm').checked = settings.email_booking_confirm == '1';
                    if (settings.email_payment_trans) document.getElementById('emailPaymentTrans').checked = settings.email_payment_trans == '1';
                    if (settings.email_system_updates) document.getElementById('emailSystemUpdates').checked = settings.email_system_updates == '1';
                    if (settings.sms_critical_alerts) document.getElementById('smsCriticalAlerts').checked = settings.sms_critical_alerts == '1';
                    if (settings.sms_booking_notif) document.getElementById('smsBookingNotif').checked = settings.sms_booking_notif == '1';
                    if (settings.sms_payment_confirm) document.getElementById('smsPaymentConfirm').checked = settings.sms_payment_confirm == '1';
                    if (settings.smtp_server) document.getElementById('smtpServer').value = settings.smtp_server;
                    if (settings.smtp_username) document.getElementById('smtpUsername').value = settings.smtp_username;
                    if (settings.smtp_password) document.getElementById('smtpPassword').value = settings.smtp_password;
                    if (settings.smtp_port) document.getElementById('smtpPort').value = settings.smtp_port;
                    if (settings.smtp_encryption) document.getElementById('smtpEncryption').value = settings.smtp_encryption;
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }
        }

        // Settings Save Functions
        async function saveGeneralSettings() {
            const button = document.getElementById('saveGeneralSettings');
            const originalText = button.innerHTML;
            
            // Collect form data
            const settings = {
                adminEmail: document.getElementById('adminEmail').value,
                contactPhone: document.getElementById('contactPhone').value,
                businessAddress: document.getElementById('businessAddress').value,
                timezone: document.getElementById('timezone').value,
                defaultLanguage: document.getElementById('defaultLanguage').value,
                currency: document.getElementById('currency').value
            };

            // Basic validation
            if (!settings.adminEmail || !settings.contactPhone) {
                alert('Please fill in all required fields!');
                return;
            }

            // Disable button
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                const response = await fetch('../api/save_settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'general',
                        settings: settings
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('General settings saved successfully! ✓');
                } else {
                    alert('Error saving settings: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving settings. Please try again.');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        async function saveSecuritySettings() {
            const button = document.getElementById('saveSecuritySettings');
            const originalText = button.innerHTML;
            
            // Collect form data
            const settings = {
                verificationMethod: document.getElementById('verificationMethod').value,
                minPasswordLength: document.getElementById('minPasswordLength').value,
                requireSpecialChars: document.getElementById('requireSpecialChars').checked,
                requireNumbers: document.getElementById('requireNumbers').checked,
                enableIpTracking: document.getElementById('enableIpTracking').checked,
                sendSecurityAlerts: document.getElementById('sendSecurityAlerts').checked
            };

            // Disable button
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                const response = await fetch('../api/save_settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'security',
                        settings: settings
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Security settings saved successfully! ✓');
                } else {
                    alert('Error saving settings: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving settings. Please try again.');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        async function saveNotificationSettings() {
            const button = document.getElementById('saveNotificationSettings');
            const originalText = button.innerHTML;
            
            // Collect form data
            const settings = {
                emailNotifications: {
                    newUser: document.getElementById('emailNewUser').checked,
                    newProperty: document.getElementById('emailNewProperty').checked,
                    bookingConfirm: document.getElementById('emailBookingConfirm').checked,
                    paymentTrans: document.getElementById('emailPaymentTrans').checked,
                    systemUpdates: document.getElementById('emailSystemUpdates').checked
                },
                smsNotifications: {
                    criticalAlerts: document.getElementById('smsCriticalAlerts').checked,
                    bookingNotif: document.getElementById('smsBookingNotif').checked,
                    paymentConfirm: document.getElementById('smsPaymentConfirm').checked
                },
                smtpSettings: {
                    server: document.getElementById('smtpServer').value,
                    username: document.getElementById('smtpUsername').value,
                    password: document.getElementById('smtpPassword').value,
                    port: document.getElementById('smtpPort').value,
                    encryption: document.getElementById('smtpEncryption').value
                }
            };

            // Basic validation
            if (!settings.smtpSettings.server || !settings.smtpSettings.username) {
                alert('Please fill in SMTP server and username!');
                return;
            }

            // Disable button
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                const response = await fetch('../api/save_settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'notifications',
                        settings: settings
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Notification settings saved successfully! ✓');
                } else {
                    alert('Error saving settings: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving settings. Please try again.');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        // Change Password Modal Functions
        function openChangePasswordModal() {
            document.getElementById('changePasswordModal').classList.add('active');
        }

        function closeChangePasswordModal() {
            document.getElementById('changePasswordModal').classList.remove('active');
            document.getElementById('changePasswordForm').reset();
        }

        async function submitChangePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            // Validation
            if (!currentPassword || !newPassword || !confirmPassword) {
                alert('Please fill in all fields!');
                return;
            }

            if (newPassword.length < 8) {
                alert('New password must be at least 8 characters long!');
                return;
            }

            if (newPassword !== confirmPassword) {
                alert('New passwords do not match!');
                return;
            }

            // Password strength validation - must contain at least one number
            const hasNumber = /[0-9]/.test(newPassword);

            if (!hasNumber) {
                alert('Password must contain at least one number');
                return;
            }

            // Disable button to prevent double submission
            const submitBtn = document.getElementById('changePasswordBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';

            try {
                const response = await fetch('../api/change_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Password changed successfully! ✓\n\nYou will be logged out for security reasons.');
                    closeChangePasswordModal();
                    
                    // Log out the user
                    setTimeout(() => {
                        window.location.href = '../api/logout.php';
                    }, 1000);
                } else {
                    alert('Error: ' + result.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while changing password. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Close Change Password modal when clicking outside
        document.getElementById('changePasswordModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeChangePasswordModal();
            }
        });

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddUserModal();
                closeChangePasswordModal();
            }
        });
    </script>
</body>
</html>





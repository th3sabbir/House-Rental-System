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
                <li><a href="../index.php">
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
                        <p>Welcome back, Admin! Here's what's happening today.</p>
                    </div>
                    <div class="header-actions">
                        <div class="user-profile">
                            <div class="admin-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="user-info">
                                <span class="user-name">Admin User</span>
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
                        <h3>All Users (156)</h3>
                        <div class="table-actions">
                            <input type="text" class="form-control" placeholder="Search users..." style="width: 250px; padding: 8px 12px;">
                            <select class="form-control" style="width: auto; padding: 8px 12px;">
                                <option>All Roles</option>
                                <option>Landlords</option>
                                <option>Tenants</option>
                                <option>Admins</option>
                            </select>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=100" alt="User" class="user-avatar">
                                        <span class="user-name-cell">Sophia Martinez</span>
                                    </div>
                                </td>
                                <td>sophia.martinez@example.com</td>
                                <td><span class="badge landlord">Landlord</span></td>
                                <td><span class="badge active">Active</span></td>
                                <td>Jan 15, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm" onclick="viewUser(1)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Details</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-warning btn-sm" onclick="editUser(1)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <span class="tooltip-text">Edit User</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm" onclick="deleteUser(1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Delete User</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://images.pexels.com/photos/1222271/pexels-photo-1222271.jpeg?auto=compress&cs=tinysrgb&w=100" alt="User" class="user-avatar">
                                        <span class="user-name-cell">Liam Harper</span>
                                    </div>
                                </td>
                                <td>liam.harper@example.com</td>
                                <td><span class="badge tenant">Tenant</span></td>
                                <td><span class="badge active">Active</span></td>
                                <td>Feb 20, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm" onclick="viewUser(2)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Details</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-warning btn-sm" onclick="editUser(2)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <span class="tooltip-text">Edit User</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm" onclick="deleteUser(2)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Delete User</span>
                                        </div>
                                    </div>
                                </td>
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
                                <td>ava.bennett@example.com</td>
                                <td><span class="badge tenant">Tenant</span></td>
                                <td><span class="badge active">Active</span></td>
                                <td>Mar 10, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn btn-info btn-sm" onclick="viewUser(3)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="editUser(3)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteUser(3)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://images.pexels.com/photos/91227/pexels-photo-91227.jpeg?auto=compress&cs=tinysrgb&w=100" alt="User" class="user-avatar">
                                        <span class="user-name-cell">Noah Foster</span>
                                    </div>
                                </td>
                                <td>noah.foster@example.com</td>
                                <td><span class="badge landlord">Landlord</span></td>
                                <td><span class="badge inactive">Inactive</span></td>
                                <td>Apr 5, 2025</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-info btn-sm" onclick="viewUser(4)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <span class="tooltip-text">View Details</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-warning btn-sm" onclick="editUser(4)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <span class="tooltip-text">Edit User</span>
                                        </div>
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-danger btn-sm" onclick="deleteUser(4)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-text">Delete User</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                    <div class="stat-card primary" style="cursor: pointer;" onclick="showSettingsTab('general')">
                        <div class="stat-icon primary">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label" style="font-size: 1.1rem; font-weight: 600;">General Settings</div>
                            <p style="font-size: 0.85rem; color: var(--text-medium); margin-top: 5px;">Site configuration & branding</p>
                        </div>
                    </div>

                    <div class="stat-card info" style="cursor: pointer;" onclick="showSettingsTab('security')">
                        <div class="stat-icon info">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-details">
                            <div class="stat-label" style="font-size: 1.1rem; font-weight: 600;">Security Settings</div>
                            <p style="font-size: 0.85rem; color: var(--text-medium); margin-top: 5px;">Authentication & access control</p>
                        </div>
                    </div>

                    <div class="stat-card warning" style="cursor: pointer;" onclick="showSettingsTab('notifications')">
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
                            <div class="form-group">
                                <label><i class="fas fa-tag"></i> Website Name</label>
                                <input type="text" class="form-control" value="AmarThikana" placeholder="Enter website name">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-align-left"></i> Tagline</label>
                                <input type="text" class="form-control" value="Your Home, Your Choice" placeholder="Enter tagline">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-file-alt"></i> Site Description</label>
                                <textarea class="form-control" rows="3" placeholder="Describe your website">Your trusted platform for finding and renting properties in Bangladesh</textarea>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-envelope"></i> Admin Email</label>
                                    <input type="email" class="form-control" value="admin@amarthikana.com" placeholder="admin@example.com">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-phone"></i> Contact Phone</label>
                                    <input type="tel" class="form-control" value="+880 1234-567890" placeholder="+880 XXXX-XXXXXX">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-map-marker-alt"></i> Business Address</label>
                                    <input type="text" class="form-control" value="Dhaka, Bangladesh" placeholder="Enter address">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-globe-asia"></i> Timezone</label>
                                    <select class="form-control">
                                        <option>Asia/Dhaka (GMT+6)</option>
                                        <option>Asia/Kolkata (GMT+5:30)</option>
                                        <option>UTC (GMT+0)</option>
                                    </select>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-language"></i> Default Language</label>
                                    <select class="form-control">
                                        <option>English</option>
                                        <option>বাংলা (Bangla)</option>
                                        <option>Hindi</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-money-bill-wave"></i> Currency</label>
                                    <select class="form-control">
                                        <option>BDT (৳)</option>
                                        <option>USD ($)</option>
                                        <option>EUR (€)</option>
                                        <option>INR (₹)</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" style="margin-top: 10px;">
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
                                <select class="form-control">
                                    <option>Email Verification Required</option>
                                    <option>Phone Verification Required</option>
                                    <option>Both Email & Phone Required</option>
                                    <option>Manual Admin Approval</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-key"></i> Minimum Password Length</label>
                                <input type="number" class="form-control" value="8" min="6" max="20">
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-spell-check"></i> Require special characters in password</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-sort-numeric-up"></i> Require numbers in password</span>
                                </label>
                            </div>

                            <h4 style="margin: 30px 0 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-shield-virus"></i> Security Features
                            </h4>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-fingerprint"></i> Enable IP Tracking & Logging</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span><i class="fas fa-exclamation-triangle"></i> Send Security Alerts via Email</span>
                                </label>
                            </div>
                            <button class="btn btn-primary">
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
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span>New user registration</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span>New property listing</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span>Booking confirmations</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span>Payment transactions</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;">
                                    <span>System updates</span>
                                </label>
                            </div>

                            <h4 style="margin: 30px 0 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-sms"></i> SMS Notifications
                            </h4>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;" checked>
                                    <span>Critical alerts only</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;">
                                    <span>Booking notifications</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" style="width: auto; margin-right: 10px;">
                                    <span>Payment confirmations</span>
                                </label>
                            </div>

                            <h4 style="margin: 30px 0 20px; color: var(--text-dark); font-size: 1.1rem;">
                                <i class="fas fa-cog"></i> Notification Settings
                            </h4>
                            <div class="form-group">
                                <label><i class="fas fa-server"></i> SMTP Server</label>
                                <input type="text" class="form-control" value="smtp.gmail.com" placeholder="smtp.example.com">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> SMTP Username</label>
                                    <input type="text" class="form-control" value="noreply@amarthikana.com">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-lock"></i> SMTP Password</label>
                                    <input type="password" class="form-control" value="••••••••">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label><i class="fas fa-plug"></i> SMTP Port</label>
                                    <input type="number" class="form-control" value="587">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-shield-alt"></i> Encryption</label>
                                    <select class="form-control">
                                        <option>TLS</option>
                                        <option>SSL</option>
                                        <option>None</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary">
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
                <h3>Add New User</h3>
                <button class="modal-close" onclick="closeAddUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>User Role *</label>
                        <select class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="landlord">Landlord</option>
                            <option value="tenant">Tenant</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeAddUserModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitAddUser()">
                    <i class="fas fa-save"></i> Add User
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
                            <li>At least one uppercase letter</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="closeChangePasswordModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitChangePassword()">
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
                });
            });
        });

        // Modal Functions
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.add('active');
        }

        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.remove('active');
            document.getElementById('addUserForm').reset();
        }

        function submitAddUser() {
            const form = document.getElementById('addUserForm');
            if (form.checkValidity()) {
                alert('User added successfully!');
                closeAddUserModal();
            } else {
                form.reportValidity();
            }
        }

        // User Management Functions
        function viewUser(id) {
            alert('Viewing user details for User ID: ' + id);
        }

        function editUser(id) {
            alert('Editing user with ID: ' + id);
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                alert('User deleted successfully!');
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddUserModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('addUserModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddUserModal();
            }
        });

        // Settings Tab Switching
        function showSettingsTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.settings-tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab
            const selectedTab = document.getElementById(tabName + 'Settings');
            if (selectedTab) {
                selectedTab.classList.add('active');
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

        function submitChangePassword() {
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

            // Password strength validation
            const hasUpperCase = /[A-Z]/.test(newPassword);
            const hasNumber = /[0-9]/.test(newPassword);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(newPassword);

            if (!hasUpperCase || !hasNumber || !hasSpecial) {
                alert('Password must contain:\n- At least one uppercase letter\n- At least one number\n- At least one special character');
                return;
            }

            // In real application, this would make an API call
            alert('Password changed successfully! ✓\n\nYou will be logged out for security reasons.');
            closeChangePasswordModal();
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

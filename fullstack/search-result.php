<!DOCTYPE html>
<html lang="en">
<head>
   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - HouseRental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        
:root {
    --primary-color: #2c3e50; /* Slate Blue */
    --secondary-color: #1abc9c; /* Vibrant Teal */
    --background-light: #fdfdfd;
    --background-white: #ffffff;
    --text-dark: #34495e;
    --text-medium: #7f8c8d;
    --border-color: #ecf0f1;
    --font-family-body: 'Lato', sans-serif;
    --font-family-heading: 'Poppins', sans-serif;
    --shadow-soft: 0 4px 15px rgba(0,0,0,0.06);
    --shadow-medium: 0 8px 25px rgba(44, 62, 80, 0.1);
    --border-radius: 12px;
}

html { scroll-behavior: smooth; }
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: var(--font-family-body);
    color: var(--text-dark);
    line-height: 1.7;
    background-color: var(--background-light);
}


h1, h2, h3, h4 {
    font-family: var(--font-family-heading);
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1.3;
}

h1 { font-size: 3.2rem; }
h2.section-title { font-size: 2.5rem; text-align: center; margin-bottom: 1rem; }
p.section-subtitle { text-align: center; max-width: 600px; margin: 0 auto 3.5rem auto; color: var(--text-medium); }
h3 { font-size: 1.35rem; }

a { text-decoration: none; color: var(--secondary-color); transition: color 0.3s ease; }
a:hover { color: #16a085; }

.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

.btn {
    display: inline-block;
    padding: 14px 32px;
    border-radius: 50px; /* Pill shape */
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid transparent;
    text-align: center;
}
.btn-primary { background-color: var(--secondary-color); color: var(--background-white); }

    .nav-actions .btn-primary:hover {
        color: white !important;
    }
    
   
    .btn-primary:hover {
        color: white !important;
    }
/* .btn-primary:hover { background-color: #16a085; transform: translateY(-3px); box-shadow: var(--shadow-medium); } */
.btn-secondary {
    background-color: transparent;
    color: var(--secondary-color);
    border: 2px solid var(--secondary-color);
}
.btn-secondary:hover { background-color: var(--secondary-color); color: var(--background-white); }

.main-header {
    background-color: #2c3e50 !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06) !important;
}
/* .main-header {
    background-color: var(--primary-color);
    box-shadow: var(--shadow-soft);
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    padding: 20px 0;
    transition: background-color 0.4s ease, box-shadow 0.4s ease, padding 0.4s ease;
}
.main-header.scrolled {
    background-color: var(--primary-color);
    box-shadow: var(--shadow-soft);
    padding: 15px 0;
} */
/* ... etc ... */
.property-card {
    background-color: var(--background-white); border-radius: var(--border-radius); overflow: hidden;
    border: 1px solid var(--border-color); transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: var(--shadow-soft);
}
.property-card:hover { transform: translateY(-10px); box-shadow: var(--shadow-medium); }
/* ... (all other existing styles) ... */
.main-footer { 
    background-color: var(--primary-color);
    padding: 60px 0 20px; 
    color: #ecf0f1;
}




.page-header {
    background-color: var(--background-white);
    padding: 15px 0;
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-soft);
}
.page-header .logo { color: var(--text-dark); }
.page-header .nav-links a { color: var(--text-dark); }

.user-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--secondary-color);
}

/* --- Dashboard Page --- */
.dashboard-layout { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
.sidebar { background-color: var(--background-white); border-right: 1px solid var(--border-color); padding: 32px; }
.sidebar-profile { text-align: center; margin-bottom: 32px; }
.sidebar-profile img { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 12px; }
.sidebar-profile h3 { font-size: 1.2rem; }
.sidebar-profile p { font-size: 0.9rem; color: var(--text-medium); margin: 0; }

.sidebar-nav { list-style: none; }
.sidebar-nav li a {
    display: flex; align-items: center; gap: 12px; padding: 14px 18px;
    border-radius: var(--border-radius); color: var(--text-medium); font-weight: 500; margin-bottom: 8px;
}
.sidebar-nav li a:hover { background-color: #f8f9fa; color: var(--text-dark); }
.sidebar-nav li a.active { background-color: var(--secondary-color); color: var(--background-white); font-weight: 600; }
.sidebar-nav li a i { width: 20px; text-align: center; font-size: 1.1rem; }

.dashboard-content { padding: 48px; background-color: #f8f9fa; }
.dashboard-content h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
.dashboard-content .welcome-message { color: var(--text-medium); margin-bottom: 2.5rem; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.section-header h2 { margin-bottom: 0; font-size: 1.8rem; }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 48px; }

.info-table { width: 100%; border-collapse: collapse; background-color: var(--background-white); border-radius: var(--border-radius); box-shadow: var(--shadow-soft); overflow: hidden; }
.info-table thead th { text-align: left; background-color: #f8f9fa; color: var(--text-medium); font-weight: 600; padding: 16px; font-size: 0.9rem; border-bottom: 2px solid var(--border-color); }
.info-table tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }
.info-table tbody tr:last-child td { border-bottom: none; }
.status-badge { padding: 5px 14px; border-radius: 50px; font-weight: 600; font-size: 0.8rem; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-accepted { background-color: #d1fae5; color: #065f46; }

/* --- Property Detail Page --- */
.page-wrapper { padding-top: 80px; } /* Offset for fixed header */
.detail-section { padding: 60px 0; }
.image-gallery { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 48px; }
.main-image img { width: 100%; height: 500px; object-fit: cover; border-radius: var(--border-radius); }
.thumb-images { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.thumb-images img { width: 100%; height: 238px; object-fit: cover; border-radius: var(--border-radius); }

.detail-layout { display: grid; grid-template-columns: 2.5fr 1.5fr; gap: 60px; }
.detail-content h1 { font-size: 2.8rem; margin-bottom: 8px; }
.detail-content .location { font-size: 1.1rem; color: var(--text-medium); margin-bottom: 24px; }
.detail-specs { display: flex; gap: 32px; padding: 24px 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
.detail-specs span { font-size: 1.1rem; font-weight: 500; }
.detail-specs i { color: var(--secondary-color); margin-right: 8px; }
.detail-section h2 { font-size: 1.8rem; margin-bottom: 1rem; margin-top: 2rem; }
.amenities-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.amenity-item { background: #f8f9fa; border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 16px; display: flex; align-items: center; gap: 12px; }
.booking-box { background: var(--background-white); padding: 32px; border-radius: var(--border-radius); box-shadow: var(--shadow-medium); position: sticky; top: 120px; }
.booking-box .btn { width: 100%; margin-top: 16px; }

/* --- Chat Page --- */
.chat-layout { display: grid; grid-template-columns: 350px 1fr; height: calc(100vh - 73px); }
.chat-sidebar { border-right: 1px solid var(--border-color); display: flex; flex-direction: column; background: var(--background-white); }
.chat-list { flex-grow: 1; overflow-y: auto; }
.chat-item { display: flex; align-items: center; gap: 12px; padding: 16px; cursor: pointer; border-bottom: 1px solid var(--border-color); }
.chat-item:hover { background-color: #f8f9fa; }
.chat-item.active { background-color: #e8f8f5; border-right: 4px solid var(--secondary-color); }
.chat-item img { width: 50px; height: 50px; border-radius: 50%; }
.chat-item h4 { font-size: 1rem; margin: 0; }
.chat-item p { font-size: 0.9rem; margin: 0; color: var(--text-medium); }

.chat-window { display: flex; flex-direction: column; background-color: #f8f9fa; }
.chat-header { padding: 16px 24px; background: var(--background-white); border-bottom: 1px solid var(--border-color); font-weight: 600; font-size: 1.2rem; }
.message-area { flex-grow: 1; padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; }
.message-bubble { max-width: 65%; padding: 14px 20px; border-radius: 20px; line-height: 1.5; }
.message-bubble .timestamp { font-size: 0.8rem; color: var(--text-medium); margin-bottom: 4px; display: block; }
.sent { background: var(--secondary-color); color: var(--background-white); align-self: flex-end; border-bottom-right-radius: 4px; }
.sent .timestamp { color: rgba(255,255,255,0.7); }
.received { background: var(--background-white); color: var(--text-dark); align-self: flex-start; border: 1px solid var(--border-color); border-bottom-left-radius: 4px; }
.message-input-form { display: flex; align-items: center; gap: 12px; padding: 16px 24px; background: var(--background-white); border-top: 1px solid var(--border-color); }
.message-input-form input { flex-grow: 1; padding: 14px; border-radius: 50px; background: #f8f9fa; border: 1px solid var(--border-color); }
.message-input-form .btn { border-radius: 50%; width: 52px; height: 52px; padding: 0; font-size: 1.2rem; }


.pagination a.active, .pagination a:hover { background: var(--secondary-color); color: var(--background-white); border-color: var(--secondary-color); }


.top-search-area .search-form {
    display: flex;
    gap: 10px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border-color);
    position: relative; /* Add this */
    z-index: 100; /* Add this */
}
.top-search-area .search-form .input-group {
    padding: 0;
    margin: 0;
    box-shadow: none;
    background: transparent;
}
.top-search-area .search-form .select-group {
    flex: 1;
}
.top-search-area .search-form select {
    padding: 12px 36px 12px 38px;
    border-radius: 30px;
    background: #f8fafc;
    border: none;
}
.top-search-area .search-form .select-group i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #16a085;
    z-index: 2;
}
.top-search-area .search-form button.btn-primary {
    border-radius: 30px;
    padding: 12px 32px;
    font-size: 1.07rem;
    margin-left: 10px;
    box-shadow: 0 2px 10px rgba(44,62,80,0.06);
}

/* Container for the top search bar */
.top-search-area {
    padding: 24px 0 0 0; /* Adds space from the header */
    margin-bottom: 24px;
}

.results-search-form {
    display: flex;
    align-items: center;
    position: relative;
    background-color: #f0f9f7; /* Light green background */
    border-radius: 100px; /* Pill shape */
    padding: 4px 8px;
    box-shadow: var(--shadow-soft);
}

.results-search-form i.fa-search {
    position: absolute;
    left: 24px;
    color: var(--text-medium);
    font-size: 1.1rem;
}

.results-search-form input {
    width: 100%;
    border: none;
    background: transparent;
    padding: 14px 50px 14px 55px;
    font-size: 1.1rem;
    font-family: var(--font-family-body);
    outline: none;
    color: var(--text-dark);
}

.results-search-form .clear-btn {
    position: absolute;
    right: 20px;
    background: #d1d8dd;
    color: var(--background-white);
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s;
}
.results-search-form .clear-btn:hover {
    background-color: #b0b8bf;
}

/* The filter bar with modern dropdowns */
.filter-bar.modern-dropdown-form {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 0;
    margin-bottom: 32px;
    border-bottom: none;
}

.modern-dropdown-form .select-group {
    position: relative;
    flex: 1;
    min-width: 150px;
}

.modern-dropdown-form select {
    width: 100%;
    padding: 10px 36px 10px 20px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--background-white);
    color: var(--text-dark);
    font-size: 0.95rem;
    font-family: inherit;
    font-weight: 500;
    outline: none;
    box-shadow: none;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    transition: box-shadow 0.2s, border-color 0.2s;
    cursor: pointer;
}

.modern-dropdown-form select:hover {
    border-color: #bdc3c7;
    box-shadow: var(--shadow-soft);
}

.modern-dropdown-form select:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
}

.modern-dropdown-form .custom-arrow {
    position: absolute;
    right: 16px;
    top: 50%;
    pointer-events: none;
    transform: translateY(-50%);
}

.modern-dropdown-form .custom-arrow::before {
    content: '';
    display: block;
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid var(--text-medium);
}

.modern-dropdown-form .input-group {
    background: transparent;
    border-radius: 0;
    box-shadow: none;
    padding: 0;
    margin: 0;
    position: relative;
    display: flex;
    align-items: center;
}

.modern-dropdown-form select option {
    font-weight: 500;
}

/* Header for the results count */
.results-header {
    margin-top: 0;
    margin-bottom: 24px;
    font-size: 2rem;
    font-weight: 600;
}

/* --- Add these styles for the custom dropdowns --- */
.custom-select-wrapper {
    position: relative;
    flex: 1;
    min-width: 150px;
}
.custom-select {
    position: relative;
    cursor: pointer;
}
.custom-select-trigger {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background: #f8fafc;
    border-radius: 30px;
    font-size: 1rem;
    font-family: var(--font-family-body);
    color: var(--text-dark);
    width: 100%;
    height: 100%;
}
.custom-select-trigger i {
    color: var(--text-medium);
    margin-right: 15px;
    font-size: 1.1rem;
}
.custom-select-trigger::after {
    content: '\f078';
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: var(--text-medium);
    transition: transform 0.2s ease;
}
.custom-select.open .custom-select-trigger::after {
    transform: translateY(-50%) rotate(180deg);
}
.custom-options {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: var(--background-white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-medium);
    z-index: 1000; /* Increased z-index */
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
}
.custom-select.open .custom-options {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.custom-option {
    padding: 12px 20px;
    cursor: pointer;
    transition: background-color 0.2s ease, color 0.2s ease;
    font-weight: 500;
    color: #222;
    border-bottom: 1px solid var(--border-color);
}
.custom-options .custom-option:last-child {
    border-bottom: none;
}
.custom-option:hover {
    background-color: #f0f9f7;
    color: var(--secondary-color);
}
.custom-option.selected {
    background-color: var(--secondary-color);
    color: var(--background-white);
}
.custom-select-wrapper select,
.custom-select-wrapper > i {
    display: none;
}
/* --- End of custom dropdown styles --- */


.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

.pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 60px; padding-bottom: 60px; }
.pagination a { padding: 10px 18px; border: 2px solid var(--border-color); border-radius: 50px; color: var(--text-dark); font-weight: 600; }
.pagination a.active, .pagination a:hover { background: var(--secondary-color); color: var(--background-white); border-color: var(--secondary-color); }
    </style>
</head>
<body>
    
<!-- Header Placeholder -->
    <div id="header-placeholder"></div>


<main class="page-wrapper">
    <div class="container">
        <!-- Main Search Form (like index.php) -->
        <div class="top-search-area">
            <form class="search-form modern-dropdown-form">
                <div class="custom-select-wrapper">
                    <i class="fas fa-map-marker-alt"></i>
                    <select required>
                        <option value="" disabled>Select City</option>
            <option value="dhaka" selected>Dhaka</option>
            <option value="chattogram">Chattogram</option>
            <option value="khulna">Khulna</option>
            <option value="rajshahi">Rajshahi</option>
            <option value="sylhet">Sylhet</option>
            <option value="barisal">Barisal</option>
            <option value="rangpur">Rangpur</option>
            <option value="mymensingh">Mymensingh</option>
                    </select>
                    <!-- <span class="custom-arrow"></span> -->
                </div>
                <!-- <div class="input-group select-group"> -->
                    <div class="custom-select-wrapper">
                    <i class="fas fa-building"></i>
                    <select required>
                        <option disabled>Property Type</option>
                        <option selected>Apartment</option>
                        <option>House</option>
                        <option>Studio</option>
                    </select>
                    <!-- <span class="custom-arrow"></span> -->
                </div>
                <!-- <div class="input-group select-group"> -->
                    <div class="custom-select-wrapper">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    <select required>
                        <option disabled>Any Price</option>
                        <option>5000 - 10000</option>
                        <option>10000 - 20000</option>
                        <option selected>20000 - 40000</option>
                    </select>
                    <!-- <span class="custom-arrow"></span> -->
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

       <div class="filter-bar modern-dropdown-form">
            <!-- <div class="input-group select-group"> -->
                <div class="custom-select-wrapper">
                <select required>
                    <option value="" disabled selected>Dates</option>
                    <option value="any">Any Date</option>
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                </select>
                <!-- <span class="custom-arrow"></span> -->
            </div>
            <!-- <div class="input-group select-group"> -->
                <div class="custom-select-wrapper">
                <select required>
                    <option value="" disabled selected>Guests</option>
                    <option value="1">1 Guest</option>
                    <option value="2">2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4+">4+ Guests</option>
                </select>
                <!-- <span class="custom-arrow"></span> -->
            </div>
            <!-- <div class="input-group select-group"> -->
                <div class="custom-select-wrapper">
                <select required>
                    <option value="" disabled selected>Price range</option>
                    <option value="any">Any Price</option>
                    <option value="<20k">Under ৳20,000</option>
                    <option value="20k-40k">৳20,000 - ৳40,000</option>
                    <option value=">40k">Over ৳40,000</option>
                </select>
                <!-- <span class="custom-arrow"></span> -->
            </div>
            <!-- <div class="input-group select-group"> -->
                <div class="custom-select-wrapper">
                <select required>
                    <option value="" disabled selected>Type of place</option>
                    <option value="any">Any Type</option>
                    <option value="apartment">Apartment</option>
                    <option value="house">House</option>
                    <option value="studio">Studio</option>
                </select>
                <!-- <span class="custom-arrow"></span> -->
            </div>
            <!-- <div class="input-group select-group"> -->
                <div class="custom-select-wrapper">
                <select required>
                    <option value="" disabled selected>More filters</option>
                    <option value="parking">Parking</option>
                    <option value="ac">Air Conditioning</option>
                    <option value="furnished">Furnished</option>
                </select>
                <!-- <span class="custom-arrow"></span> -->
            </div>
        </div>

            
<h2 class="results-header">1,23 Homes found in Dhaka</h2>

<div class="results-grid">
    
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/1643383/pexels-photo-1643383.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Modern Family Flat in Gulshan"></div>
        <div class="card-content">
            <h3>Modern Family Flat in Gulshan</h3>
            <p class="address">House #12, Road #5, Gulshan, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 3 Beds</span>
                <span><i class="fas fa-bath"></i> 3 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 1,450 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 25,000/mo</div>
        </div>
    </div>
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Luxury Apartment Bashundhara"></div>
        <div class="card-content">
            <h3>Luxury Apartment Bashundhara</h3>
            <p class="address">Flat #B2, Block C, Bashundhara, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 4 Beds</span>
                <span><i class="fas fa-bath"></i> 4 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 2,000 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 40,000/mo</div>
        </div>
    </div>
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/5998120/pexels-photo-5998120.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Cozy Studio in Dhanmondi"></div>
        <div class="card-content">
            <h3>Cozy Studio in Dhanmondi</h3>
            <p class="address">House #8, Road #27, Dhanmondi, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 1 Bed</span>
                <span><i class="fas fa-bath"></i> 1 Bath</span>
                <span><i class="fas fa-ruler-combined"></i> 650 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 12,000/mo</div>
        </div>
    </div>
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/210617/pexels-photo-210617.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Spacious House in Uttara"></div>
        <div class="card-content">
            <h3>Spacious House in Uttara</h3>
            <p class="address">House #A3, Sector 7, Uttara, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 5 Beds</span>
                <span><i class="fas fa-bath"></i> 4 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 2,500 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 55,000/mo</div>
        </div>
    </div>
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Elegant Flat in Banani"></div>
        <div class="card-content">
            <h3>Elegant Flat in Banani</h3>
            <p class="address">Flat #22, Road #11, Banani, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 3 Beds</span>
                <span><i class="fas fa-bath"></i> 3 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 1,600 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 32,000/mo</div>
        </div>
    </div>
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/276724/pexels-photo-276724.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Family Apartment Mirpur"></div>
        <div class="card-content">
            <h3>Family Apartment Mirpur</h3>
            <p class="address">Flat #5C, Mirpur DOHS, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 3 Beds</span>
                <span><i class="fas fa-bath"></i> 2 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 1,200 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 20,000/mo</div>
        </div>
    </div>
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/208736/pexels-photo-208736.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Serviced Apartment Motijheel"></div>
        <div class="card-content">
            <h3>Serviced Apartment Motijheel</h3>
            <p class="address">Flat #3B, Motijheel, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 2 Beds</span>
                <span><i class="fas fa-bath"></i> 2 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 950 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 18,500/mo</div>
        </div>
    </div>
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/1396132/pexels-photo-1396132.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Penthouse Bashundhara"></div>
        <div class="card-content">
            <h3>Penthouse Bashundhara</h3>
            <p class="address">Penthouse, Block D, Bashundhara, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 6 Beds</span>
                <span><i class="fas fa-bath"></i> 5 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 3,200 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 90,000/mo</div>
        </div>
    </div>
    
    <div class="property-card">
        <div class="card-image"><img src="https://images.pexels.com/photos/323780/pexels-photo-323780.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Green View Apartment Badda"></div>
        <div class="card-content">
            <h3>Green View Apartment Badda</h3>
            <p class="address">Flat #7A, North Badda, Dhaka</p>
            <div class="property-specs">
                <span><i class="fas fa-bed"></i> 2 Beds</span>
                <span><i class="fas fa-bath"></i> 2 Baths</span>
                <span><i class="fas fa-ruler-combined"></i> 950 sqft</span>
            </div>
            <div style="font-weight:600; color:#16a085; margin-top:8px;">৳ 16,000/mo</div>
        </div>
    </div>
</div>

</div>


            <div class="pagination">
                <a href="#">First</a>
                <a href="#">1</a>
                <a href="#" class="active">2</a>
                <a href="#">3</a>
                <a href="#">Next</a>
            </div>
        </div>
    </main>

    
    <!-- Footer Placeholder -->
    <div id="footer-placeholder"></div>

    <!-- Scripts -->
    <script src="js/loader.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.custom-select-wrapper').forEach(function(wrapper) {
        const originalSelect = wrapper.querySelector('select');
        const icon = wrapper.querySelector('i');

        // Create custom dropdown structure
        const customSelect = document.createElement('div');
        customSelect.classList.add('custom-select');
        
        const trigger = document.createElement('div');
        trigger.classList.add('custom-select-trigger');
        
        const options = document.createElement('div');
        options.classList.add('custom-options');

        // Move icon inside the trigger
        if (icon) {
            trigger.appendChild(icon.cloneNode(true));
            icon.style.display = 'none'; // Hide original icon
        }
        
        const triggerText = document.createElement('span');
        trigger.appendChild(triggerText);
        
        customSelect.appendChild(trigger);
        customSelect.appendChild(options);
        wrapper.appendChild(customSelect);

        // Populate custom options
        Array.from(originalSelect.options).forEach(function(optionElement) {
            const customOption = document.createElement('div');
            customOption.classList.add('custom-option');
            customOption.textContent = optionElement.textContent;
            customOption.dataset.value = optionElement.value;
            
            if (optionElement.selected) {
                triggerText.textContent = optionElement.textContent;
                customOption.classList.add('selected');
            }
            if (optionElement.disabled) {
                customOption.style.display = 'none';
            }

            customOption.addEventListener('click', function() {
                if (customSelect.querySelector('.custom-option.selected')) {
                    customSelect.querySelector('.custom-option.selected').classList.remove('selected');
                }
                this.classList.add('selected');
                triggerText.textContent = this.textContent;
                originalSelect.value = this.dataset.value;
                customSelect.classList.remove('open');
            });
            
            options.appendChild(customOption);
        });

        // Toggle dropdown
        trigger.addEventListener('click', function() {
            customSelect.classList.toggle('open');
        });
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        document.querySelectorAll('.custom-select').forEach(function(select) {
            if (!select.contains(e.target)) {
                select.classList.remove('open');
            }
        });
    });
});
</script>
</body>
</html>

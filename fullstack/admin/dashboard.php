<!-- Dashboard Section -->
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

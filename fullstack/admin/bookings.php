<!-- Bookings Section -->
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

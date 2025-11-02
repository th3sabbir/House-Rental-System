<!-- Properties Section -->
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

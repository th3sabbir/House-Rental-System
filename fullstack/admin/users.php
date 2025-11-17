<!-- Users Management Section -->
<div class="content-header">
    <div class="header-title">
        <h1>Users Management</h1>
        <p>Manage all registered users and their permissions</p>
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
                <th>Joined Date</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
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

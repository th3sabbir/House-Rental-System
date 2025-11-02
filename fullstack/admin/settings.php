<!-- Settings Section -->
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

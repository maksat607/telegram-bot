<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Telegram Bot Webhook Management</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.0/axios.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0088cc, #0066aa);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1em;
        }

        .content {
            padding: 40px;
        }

        .webhook-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0088cc;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0088cc;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: #0088cc;
            color: white;
        }

        .btn-primary:hover {
            background: #0066aa;
            transform: translateY(-2px);
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .webhook-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .info-item {
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #0088cc;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .info-value {
            color: #666;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0088cc;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🤖 Telegram Bot</h1>
        <p>Webhook Management Dashboard</p>
    </div>

    <div class="content">
        <!-- Alert Messages -->
        <div id="alert-container"></div>

        <!-- Loading Indicator -->
        <div id="loading" class="loading">
            <div class="spinner"></div>
            <p>Processing...</p>
        </div>

        <!-- Set Webhook Section -->
        <div class="webhook-section">
            <h2 class="section-title">🔗 Set Webhook</h2>
            <div class="form-group">
                <label for="webhook-url">Webhook URL</label>
                <input type="url" id="webhook-url" placeholder="https://yourapp.com/telegram/webhook"
                       value="{{ url('api/telegram-bot') }}">
            </div>
            <button class="btn btn-primary" onclick="setWebhook()">
                Set Webhook
            </button>
        </div>

        <!-- Webhook Actions -->
        <div class="webhook-section">
            <h2 class="section-title">🎛️ Webhook Actions</h2>
            <button class="btn btn-info" onclick="getWebhookInfo()">
                Get Webhook Info
            </button>
            <button class="btn btn-danger" onclick="deleteWebhook()">
                Delete Webhook
            </button>
        </div>

        <!-- Webhook Information Display -->
        <div id="webhook-info" class="webhook-info" style="display: none;">
            <h3 style="margin-bottom: 15px; color: #333;">📊 Current Webhook Information</h3>
            <div id="webhook-details"></div>
        </div>
    </div>
</div>

<script>
    // Setup CSRF token for axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Get token from localStorage
    const token = localStorage.getItem('token');
    console.log('Using token:', token);

    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alert-container');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';

        alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    ${message}
                </div>
            `;

        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    function showLoading(show = true) {
        document.getElementById('loading').style.display = show ? 'block' : 'none';
    }

    async function setWebhook() {
        const webhookUrl = document.getElementById('webhook-url').value;

        if (!webhookUrl) {
            showAlert('Please enter a webhook URL', 'error');
            return;
        }

        if (!token) {
            showAlert('No authentication token found. Please log in.', 'error');
            console.log('setWebhook: No token found in localStorage');
            return;
        }

        try {
            showLoading(true);
            console.log('setWebhook: Sending POST request to /api/telegram/set-webhook with URL:', webhookUrl, 'and token:', token);
            const response = await axios.post('/api/telegram/set-webhook', {
                webhook_url: webhookUrl
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            console.log('setWebhook: Response received:', response.data);
            if (response.data.success) {
                showAlert('✅ Webhook set successfully!');
                getWebhookInfo(); // Refresh webhook info
            } else {
                showAlert('❌ ' + response.data.message, 'error');
                console.log('setWebhook: Server returned error:', response.data.message);
            }
        } catch (error) {
            console.error('setWebhook: Error:', error);
            console.log('setWebhook: Error details:', {
                message: error.message,
                response: error.response ? {
                    status: error.response.status,
                    data: error.response.data,
                    headers: error.response.headers
                } : 'No response data'
            });
            showAlert('❌ Error: ' + (error.response?.data?.message || error.message), 'error');
        } finally {
            showLoading(false);
        }
    }

    async function getWebhookInfo() {
        if (!token) {
            showAlert('No authentication token found. Please log in.', 'error');
            console.log('getWebhookInfo: No token found in localStorage');
            document.getElementById('webhook-info').style.display = 'none';
            return;
        }

        try {
            showLoading(true);
            console.log('getWebhookInfo: Sending GET request to /api/telegram/get-webhook with token:', token);
            const response = await axios.get('/api/telegram/get-webhook', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            console.log('getWebhookInfo: Response received:', response.data);
            if (response.data.success) {
                displayWebhookInfo(response.data.data);
                showAlert('📊 Webhook information retrieved successfully!');
            } else {
                showAlert('❌ ' + response.data.message, 'error');
                console.log('getWebhookInfo: Server returned error:', response.data.message);
                document.getElementById('webhook-info').style.display = 'none';
            }
        } catch (error) {
            console.error('getWebhookInfo: Error:', error);
            console.log('getWebhookInfo: Error details:', {
                message: error.message,
                response: error.response ? {
                    status: error.response.status,
                    data: error.response.data,
                    headers: error.response.headers
                } : 'No response data'
            });
            showAlert('❌ Error: ' + (error.response?.data?.message || error.message), 'error');
            document.getElementById('webhook-info').style.display = 'none';
        } finally {
            showLoading(false);
        }
    }

    async function deleteWebhook() {
        if (!confirm('Are you sure you want to delete the webhook?')) {
            console.log('deleteWebhook: User cancelled deletion');
            return;
        }

        if (!token) {
            showAlert('No authentication token found. Please log in.', 'error');
            console.log('deleteWebhook: No token found in localStorage');
            return;
        }

        try {
            showLoading(true);
            console.log('deleteWebhook: Sending DELETE request to /api/telegram/delete-webhook with token:', token);
            const response = await axios.delete('/api/telegram/delete-webhook', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            console.log('deleteWebhook: Response received:', response.data);
            if (response.data.success) {
                showAlert('🗑️ Webhook deleted successfully!');
                document.getElementById('webhook-info').style.display = 'none';
            } else {
                showAlert('❌ ' + response.data.message, 'error');
                console.log('deleteWebhook: Server returned error:', response.data.message);
            }
        } catch (error) {
            console.error('deleteWebhook: Error:', error);
            console.log('deleteWebhook: Error details:', {
                message: error.message,
                response: error.response ? {
                    status: error.response.status,
                    data: error.response.data,
                    headers: error.response.headers
                } : 'No response data'
            });
            showAlert('❌ Error: ' + (error.response?.data?.message || error.message), 'error');
        } finally {
            showLoading(false);
        }
    }

    function displayWebhookInfo(data) {
        console.log('displayWebhookInfo: Displaying webhook info:', data);
        const webhookInfo = document.getElementById('webhook-info');
        const webhookDetails = document.getElementBy衡量webhook-details');

        const hasWebhook = data.url && data.url !== '';
        const statusBadge = hasWebhook ?
            '<span class="status-badge status-active">Active</span>' :
            '<span class="status-badge status-inactive">Inactive</span>';

        webhookDetails.innerHTML = `
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">${statusBadge}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">URL</div>
                    <div class="info-value">${data.url || 'No webhook set'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Has Custom Certificate</div>
                    <div class="info-value">${data.has_custom_certificate ? 'Yes' : 'No'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Pending Update Count</div>
                    <div class="info-value">${data.pending_update_count || 0}</div>
                </div>
                ${data.last_error_date ? `
                <div class="info-item">
                    <div class="info-label">Last Error Date</div>
                    <div class="info-value">${new Date(data.last_error_date * 1000).toLocaleString()}</div>
                </div>
                ` : ''}
                ${data.last_error_message ? `
                <div class="info-item">
                    <div class="info-label">Last Error Message</div>
                    <div class="info-value">${data.last_error_message}</div>
                </div>
                ` : ''}
                ${data.max_connections ? `
                <div class="info-item">
                    <div class="info-label">Max Connections</div>
                    <div class="info-value">${data.max_connections}</div>
                </div>
                ` : ''}
                ${data.allowed_updates && data.allowed_updates.length > 0 ? `
                <div class="info-item">
                    <div class="info-label">Allowed Updates</div>
                    <div class="info-value">${data.allowed_updates.join(', ')}</div>
                </div>
                ` : ''}
            `;

        webhookInfo.style.display = 'block';
    }

    // Load webhook info on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded: Initializing webhook info load');
        getWebhookInfo();
    });
</script>
</body>
</html>

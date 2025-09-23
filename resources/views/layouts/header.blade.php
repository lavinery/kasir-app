<style>
    /* Gen Z Header Styling - MINIMAL CHANGES ONLY */
    .main-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border-bottom: none !important;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3) !important;
    }
    
    /* Logo styling */
    .main-header .logo {
        background: rgba(255, 255, 255, 0.15) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.2) !important;
        transition: all 0.3s ease;
    }
    
    .main-header .logo:hover {
        background: rgba(255, 255, 255, 0.25) !important;
    }
    
    .main-header .logo .logo-mini,
    .main-header .logo .logo-lg {
        color: white !important;
        font-weight: 600;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    /* Navbar styling */
    .main-header .navbar {
        background: transparent !important;
    }
    
    /* Sidebar toggle button */
    .main-header .sidebar-toggle {
        color: white !important;
        background: rgba(255, 255, 255, 0.1) !important;
        border-radius: 8px !important;
        margin: 8px !important;
        padding: 8px 12px !important;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }
    
    .main-header .sidebar-toggle:hover {
        background: rgba(255, 255, 255, 0.2) !important;
    }
    
    /* User dropdown styling */
    .main-header .navbar-nav > li > a {
        color: white !important;
        transition: all 0.3s ease;
    }
    
    .main-header .navbar-nav > .user-menu > a {
        background: rgba(255, 255, 255, 0.1) !important;
        border-radius: 25px !important;
        margin: 8px !important;
        padding: 5px 15px 5px 5px !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .main-header .navbar-nav > .user-menu > a:hover {
        background: rgba(255, 255, 255, 0.2) !important;
    }
    
    /* User image styling */
    .main-header .user-image {
        border: 2px solid rgba(255, 255, 255, 0.5) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    
    /* Username text */
    .main-header .hidden-xs {
        font-weight: 600;
        text-shadow: 0 1px 5px rgba(0,0,0,0.2);
        margin-left: 8px;
    }
    
    /* Dropdown menu styling - ONLY VISUAL CHANGES */
    .user-menu .dropdown-menu {
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px !important;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3) !important;
        overflow: hidden;
    }
    
    .user-menu .dropdown-menu::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    }
    
    /* User header in dropdown */
    .user-menu .user-header {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%) !important;
        border-bottom: 1px solid rgba(102, 126, 234, 0.2) !important;
        padding: 20px;
    }
    
    .user-menu .user-header img {
        border: 3px solid rgba(102, 126, 234, 0.5) !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .user-menu .user-header p {
        color: #333 !important;
        font-weight: 600;
        margin-top: 10px;
    }
    
    /* User footer buttons */
    .user-menu .user-footer {
        background: rgba(255, 255, 255, 0.8) !important;
        padding: 15px;
    }
    
    .user-menu .user-footer .btn {
        border-radius: 10px !important;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .user-menu .user-footer .btn-default {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border-color: transparent !important;
    }
    
    .user-menu .user-footer .btn-default:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
    }
    
    /* DateTime display styling */
    .datetime-display {
        background: rgba(255, 255, 255, 0.1) !important;
        border-radius: 8px !important;
        margin: 8px 10px !important;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .datetime-display:hover {
        background: rgba(255, 255, 255, 0.15) !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-header .navbar-nav > .user-menu > a {
            margin: 5px !important;
            padding: 3px 10px 3px 3px !important;
        }

        .main-header .hidden-xs {
            font-size: 13px;
        }

        .datetime-display {
            padding: 10px 12px !important;
            margin: 5px !important;
            font-size: 12px !important;
        }
    }

    @media (max-width: 480px) {
        .datetime-display {
            display: none !important;
        }
    }
</style>

<header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        @php
            $words = explode(' ', $setting->nama_perusahaan);
            $word  = '';
            foreach ($words as $w) {
                $word .= $w[0];
            }
        @endphp
        <span class="logo-mini">{{ $word }}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>{{ $setting->nama_perusahaan }}</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- DateTime Display -->
                <li class="datetime-display" style="padding: 15px 20px; color: white; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                    <span id="current-datetime">{{ now()->setTimezone('Asia/Jakarta')->format('d M Y - H:i:s') }} WIB</span>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ url(auth()->user()->foto ?? '') }}" class="user-image img-profil"
                            alt="User Image">
                        <span class="hidden-xs">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="{{ url(auth()->user()->foto ?? '') }}" class="img-circle img-profil"
                                alt="User Image">

                            <p>
                                {{ auth()->user()->name }} - {{ auth()->user()->email }}
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('user.profil') }}" class="btn btn-default btn-flat">Profil</a>
                            </div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-default btn-flat"
                                    onclick="$('#logout-form').submit()">Keluar</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<form action="{{ route('logout') }}" method="post" id="logout-form" style="display: none;">
    @csrf
</form>

<script>
function updateDateTime() {
    // Check if moment.js is available, if not use native Date
    if (typeof moment !== 'undefined') {
        // Using moment.js for better browser compatibility
        // Add 7 hours for WIB timezone (UTC+7)
        const now = moment().utc().add(7, 'hours');
        const formattedDateTime = now.format('DD MMM YYYY - HH:mm:ss') + ' WIB';

        const element = document.getElementById('current-datetime');
        if (element) {
            element.textContent = formattedDateTime;
        }
    } else {
        // Fallback to native JavaScript
        const now = new Date();

        // Manual timezone conversion to WIB (UTC+7)
        const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        const wibTime = new Date(utc + (7 * 3600000));

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                       'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        const day = String(wibTime.getDate()).padStart(2, '0');
        const month = months[wibTime.getMonth()];
        const year = wibTime.getFullYear();
        const hours = String(wibTime.getHours()).padStart(2, '0');
        const minutes = String(wibTime.getMinutes()).padStart(2, '0');
        const seconds = String(wibTime.getSeconds()).padStart(2, '0');

        const formattedDateTime = `${day} ${month} ${year} - ${hours}:${minutes}:${seconds} WIB`;

        const element = document.getElementById('current-datetime');
        if (element) {
            element.textContent = formattedDateTime;
        }
    }
}

// Wait for moment.js to load if it's being loaded asynchronously
function initDateTime() {
    updateDateTime();
    setInterval(updateDateTime, 1000);
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDateTime);
} else {
    initDateTime();
}
</script>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /jemuran_auto/frontend/auth/login.php");
    exit;
}

require_once __DIR__ . '/../../backend/config/database.php';

$user = $_SESSION['user'];
$BASE = "/jemuran_auto/";

/* ===== AMBIL DATA DEVICE ADMIN ===== */
$sql = "
    SELECT d.id, d.nama_device, d.status, u.nama AS username
    FROM devices d
    JOIN users u ON d.user_id = u.id
    ORDER BY d.id ASC
";
$res = $conn->query($sql);
$devices = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

$total = count($devices);
$active = count(array_filter($devices, fn($d) => $d['status'] === 'open'));
$offline = $total - $active;

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<style>
body { background: #FFFBFF; font-family: "Inter", sans-serif; }
.dashboard-container { padding: 30px 50px; }

/* ===== CARDS ===== */
.stats-cards {
    display: flex; gap: 20px; flex-wrap: wrap;
    justify-content: center; margin-bottom: 40px;
}
.card-stat {
    flex: 1 1 250px; background: #f8f5ff;
    border-radius: 15px; text-align: center;
    padding: 20px; color: #2b0f53; font-weight: 700;
    transition: .3s; border: 2px solid transparent;
}
.card-stat.active { border-color: #4a41ff; background: #edf0ff; }
.card-stat h2 { font-size: 48px; margin-top: 5px; }

/* ===== ERROR BANNER ===== */
.error-banner {
    display: none; background: #ffdddd; color: #a30000;
    padding: 12px 18px; margin-bottom: 20px;
    border-left: 5px solid #d10000; border-radius: 6px;
    font-weight: 600;
}

/* ===== TABLE ===== */
.table-box {
    background: #fff; border-radius: 15px; padding: 20px;
    box-shadow: 0 6px 20px rgba(80,60,160,0.08);
    overflow-x: auto;
}
table { width: 100%; border-collapse: collapse; }
th, td { padding: 15px 10px; border-bottom: 1px solid #eee; }
th { color: #6b5a8e; font-weight: 600; }

/* ===== TOGGLE ===== */
.toggle-switch {
    position: relative;             /* ★ FIX: agar loader muncul */
    width: 70px; height: 32px;
    background: #ADB1EF;
    border-radius: 50px;
    cursor: pointer;
    transition: .3s;
}
.toggle-slider {
    position: absolute; top: 3px; left: 3px;
    width: 26px; height: 26px;
    background: #fff; border-radius: 50%;
    transition: .3s;
}
.toggle-switch.on { background: #7b7ce0; }
.toggle-switch.on .toggle-slider { left: 41px; }
.toggle-label { font-weight: 600; width: 50px; }

/* loader kecil */
.toggle-loader {
    position: absolute;
    top: 50%; left: 50%;
    width: 18px; height: 18px;
    margin-left: -9px;
    margin-top: -9px;
    border: 2px solid rgba(255,255,255,0.6);
    border-top-color: #4a41ff;
    border-radius: 50%;
    animation: spin .6s linear infinite;
    display: none;
    z-index: 10;                     /* ★ FIX: loader di atas slider */
}

/* disable toggle while loading */
.toggle-switch.loading {
    pointer-events: none;
    opacity: 0.6;
}

@keyframes spin { 
    0% { transform: rotate(0); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="dashboard-container">

    <div class="error-banner" id="error-banner"></div>

    <!-- ===== STATISTIK ===== -->
    <div class="stats-cards">
        <div class="card-stat">
            <div>Total Perangkat</div>
            <h2 id="stat-total"><?= $total ?></h2>
        </div>
        <div class="card-stat active">
            <div>Perangkat Aktif</div>
            <h2 id="stat-active"><?= $active ?></h2>
        </div>
        <div class="card-stat">
            <div>Perangkat Offline</div>
            <h2 id="stat-offline"><?= $offline ?></h2>
        </div>
    </div>

    <!-- ===== TABLE ===== -->
    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Device</th>
                    <th>Control</th>
                </tr>
            </thead>

            <tbody id="device-table-body">
            <?php if (empty($devices)): ?>
                <tr><td colspan="3" style="text-align:center;color:#888;">Tidak ada perangkat.</td></tr>

            <?php else: foreach ($devices as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['username']) ?></td>
                    <td><?= htmlspecialchars($d['nama_device']) ?></td>

                    <td>
                        <div class="toggle-wrapper" style="display:flex;align-items:center;gap:10px;">
                            <span class="toggle-label"><?= $d['status'] === 'open' ? 'ON' : 'OFF' ?></span>

                            <div class="toggle-switch <?= $d['status'] === 'open' ? 'on' : '' ?>"
                                 data-id="<?= $d['id'] ?>">
                                <div class="toggle-slider"></div>
                                <div class="toggle-loader"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    initToggleButtons();
    startAutoRefresh();
});

/* ===== ERROR BANNER ===== */
function showError(msg) {
    const b = document.getElementById("error-banner");
    b.textContent = msg;
    b.style.display = "block";
    setTimeout(() => b.style.display = "none", 3000);
}

/* Rebind click listener clean */
function initToggleButtons() {
    document.querySelectorAll(".toggle-switch").forEach(el => {
        const clone = el.cloneNode(true);
        el.replaceWith(clone);
    });

    document.querySelectorAll(".toggle-switch").forEach(el => {
        el.addEventListener("click", () => handleToggle(el));
    });
}

/* Counter updater */
function adjustCounters(delta) {
    const active = document.getElementById("stat-active");
    const offline = document.getElementById("stat-offline");

    let a = parseInt(active.textContent);
    a += delta;
    active.textContent = a;

    offline.textContent = parseInt(document.getElementById("stat-total").textContent) - a;
}

/* ===== TOGGLE HANDLER ===== */
async function handleToggle(el) {
    const label = el.parentElement.querySelector(".toggle-label");
    const loader = el.querySelector(".toggle-loader");
    const id = el.dataset.id;

    const prevOn = el.classList.contains("on");
    const willBeOn = !prevOn;
    const newState = willBeOn ? "open" : "close";

    /* Optimistic UI */
    el.classList.toggle("on");
    el.classList.add("loading");
    label.textContent = willBeOn ? "ON" : "OFF";
    loader.style.display = "block";

    adjustCounters(willBeOn ? 1 : -1);

    try {
        const res = await fetch("/jemuran_auto/backend/device/device_control.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ id, status: newState })
        });

        const data = await res.json();
        if (!data.success) throw new Error();

        el.classList.remove("loading");
        loader.style.display = "none";

    } catch {
        /* Rollback */
        el.classList.toggle("on");
        el.classList.remove("loading");
        loader.style.display = "none";

        adjustCounters(willBeOn ? -1 : 1);
        label.textContent = prevOn ? "ON" : "OFF";

        showError("Gagal mengubah status perangkat!");
    }
}

/* ===== AUTO REFRESH ===== */
function startAutoRefresh() {
    setInterval(async () => {
        try {
            const res = await fetch("/jemuran_auto/backend/device/get_device.php");
            const data = await res.json();

            const total = data.length;
            const active = data.filter(x => x.status === "open").length;
            const offline = total - active;

            document.getElementById("stat-total").textContent = total;
            document.getElementById("stat-active").textContent = active;
            document.getElementById("stat-offline").textContent = offline;

            /* update toggle states */
            document.querySelectorAll("#device-table-body tr").forEach(tr => {
                const toggle = tr.querySelector(".toggle-switch");
                if (!toggle || toggle.classList.contains("loading")) return;

                const device = data.find(x => x.id == toggle.dataset.id);
                const label = tr.querySelector(".toggle-label");

                if (!device) return;

                if (device.status === "open") {
                    toggle.classList.add("on");
                    label.textContent = "ON";
                } else {
                    toggle.classList.remove("on");
                    label.textContent = "OFF";
                }
            });

        } catch {}
    }, 5000);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
// DB connection config (same as before)
$host = 'sql109.infinityfree.com';
$dbname = 'if0_38583332_webcraft';
$username = 'if0_38583332';
$password = 'Devsprint';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$stmt = $pdo->prepare("SELECT * FROM teams ORDER BY created_at DESC");
$stmt->execute();
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getStatusStep($status) {
    return match(strtolower($status)) {
        'submitted' => 0,
        'accepted' => 1,
        'selected' => 2,
        default => 0,
    };
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Teams Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
<style>
    body {
        background: #020617; /* slate-950 */
        color: #e2e8f0; /* slate-200 */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0; padding: 1rem;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    h1 {
        text-align: center;
        font-weight: 700;
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: #67e8f9; /* cyan-300 */
        text-shadow: 0 0 8px #22d3eeaa;
    }

    .teams-container {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        align-items: center;
    }

    .team {
        background: rgba(255 255 255 / 0.05);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255 255 255 / 0.08);
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        width: 100%;
        max-width: 1200px;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .progress-bar {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 100%;
    }

    .progress-bar-line-bg {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 6px;
        background: #1e293b; /* slate-800 */
        border-radius: 9999px;
        transform: translateY(-50%);
    }

    .progress-bar-line-fill {
        position: absolute;
        top: 50%;
        left: 0;
        height: 6px;
        background: #22c55e; /* green-500 */
        border-radius: 9999px;
        transform: translateY(-50%);
        transition: width 0.5s ease;
    }

    .progress-dot {
        z-index: 3;
        width: 22px;
        height: 22px;
        background: #475569; /* slate-600 */
        border: 3px solid #0f172a;
        border-radius: 50%;
    }

    .progress-dot.active {
        background: #22c55e;
        box-shadow: 0 0 8px 3px #22c55eaa;
    }

    .progress-dot.current {
        animation: pulse 1.5s infinite;
        box-shadow: 0 0 12px 4px #22c55ecc;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    .progress-labels {
        display: flex;
        justify-content: space-between;
        max-width: 100%;
        font-weight: 600;
        font-size: 0.9rem;
        color: #cbd5e1;
        margin-top: 0.5rem;
    }

    .members-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
    }

    .member-card {
        background: rgba(255 255 255 / 0.08);
        border-radius: 1rem;
        padding: 1rem 1.5rem;
        box-shadow: 0 6px 18px rgba(0,0,0,0.4);
        flex: 1 1 260px;
        max-width: 280px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: #f8fafc; /* slate-50 */
    }

    .member-card:hover {
        transform: scale(1.03);
        box-shadow: 0 12px 24px rgba(16, 185, 129, 0.6);
    }

    .member-info strong {
        color: #86efac; /* green-300 */
        font-size: 1.1rem;
    }

    .member-info span {
        color: #cbd5e1;
    }

    .github-link {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        color: #94a3b8;
        font-weight: 600;
        text-decoration: none;
    }

    .github-link:hover {
        color: #22d3ee;
    }

    .github-link svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .repo-link {
        font-size: 0.95rem;
        margin-top: 1rem;
    }

    .repo-link a {
        color: #67e8f9;
        text-decoration: underline;
    }

    .repo-link a:hover {
        color: #a5f3fc;
    }

    .team-footer {
        font-size: 0.85rem;
        color: #94a3b8;
        line-height: 1.5;
    }
    .project-repo-card {
    background-color: #1f2937; /* Tailwind gray-800 */
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 1.5rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

</style>


</head>
<body>
    <h1>Teams Dashboard</h1>

    <div class="teams-container">
        <?php foreach ($teams as $team):
            $status = strtolower($team['status'] ?? 'submitted');
            $step = getStatusStep($status);
            $stepsCount = 3;
            $lineWidthPercent = ($step) / ($stepsCount - 1) * 100;
        ?>

        <section class="team" aria-labelledby="team-<?= htmlspecialchars($team['id']) ?>-name" role="region">

            <div class="progress-bar" aria-label="Contest progress">
                <div class="progress-bar-line-bg"></div>
                <div class="progress-bar-line-fill" style="width: <?= $lineWidthPercent ?>%;"></div>

                <?php
                $labels = ['Submitted', 'Accepted', 'Selected'];
                for ($i = 0; $i < $stepsCount; $i++):
                    $isActive = $i <= $step;
                    $isCurrent = $i === $step;
                ?>
                    <div
                        class="progress-dot <?= $isActive ? 'active' : '' ?> <?= $isCurrent ? 'current' : '' ?>"
                        aria-current="<?= $isCurrent ? 'step' : 'false' ?>"
                        tabindex="0"
                        title="<?= $labels[$i] ?>"
                        role="button"
                        aria-label="Status: <?= $labels[$i] ?>"
                    ></div>
                <?php endfor; ?>
            </div>
            <div class="progress-labels" aria-hidden="true">
                <?php foreach ($labels as $label): ?>
                    <div><?= $label ?></div>
                <?php endforeach; ?>
            </div>

            <h2 id="team-<?= htmlspecialchars($team['id']) ?>-name" style="color:#e0e7ff; margin-bottom: 1rem; font-weight: 700; font-size: 1.75rem;">
                <?= htmlspecialchars($team['teamName']) ?>
            </h2>

            <div class="members-container">
                <!-- Leader card -->
                <article class="member-card" aria-label="Team Leader">
                    <div class="member-info">
                        <strong>Leader Name</strong>
                        <span><?= htmlspecialchars($team['leaderName']) ?></span>
                    </div>
                    <div class="member-info">
                        <strong>Email</strong>
                        <span><?= htmlspecialchars($team['leaderEmail']) ?></span>
                    </div>
                    <div class="member-info">
                        <strong>Phone</strong>
                        <span><?= htmlspecialchars($team['leaderPhone']) ?></span>
                    </div>
                    <div class="member-info">
                        <strong>GitHub</strong>
                        <?php if ($team['leaderGithub']): ?>
                            <a class="github-link" href="<?= htmlspecialchars($team['leaderGithub']) ?>" target="_blank" rel="noopener noreferrer">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0C5.372 0 0 5.372 0 12c0 5.302 3.438 9.8 8.205 11.385.6.111.82-.261.82-.58 0-.287-.011-1.244-.017-2.446-3.338.726-4.042-1.61-4.042-1.61-.546-1.388-1.333-1.757-1.333-1.757-1.09-.745.083-.73.083-.73 1.205.084 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.775.42-1.305.763-1.605-2.665-.303-5.467-1.332-5.467-5.931 0-1.31.469-2.381 1.235-3.221-.124-.303-.536-1.522.117-3.176 0 0 1.008-.322 3.301 1.23a11.52 11.52 0 0 1 3.003-.404c1.02.005 2.045.137 3.003.404 2.291-1.552 3.297-1.23 3.297-1.23.655 1.654.243 2.873.12 3.176.77.84 1.234 1.91 1.234 3.22 0 4.61-2.807 5.625-5.48 5.921.431.372.816 1.103.816 2.222 0 1.606-.015 2.9-.015 3.296 0 .322.216.698.824.58C20.565 21.796 24 17.3 24 12c0-6.628-5.372-12-12-12z"/>
                                </svg>
                                GitHub
                            </a>
                        <?php else: ?>
                            <span>N/A</span>
                        <?php endif; ?>
                    </div>
                </article>

                <!-- Member 2 card -->
                <?php if (!empty($team['member2Name']) || !empty($team['member2Email']) || !empty($team['member2Github'])): ?>
                <article class="member-card" aria-label="Team Member 2">
                    <div class="member-info">
                        <strong>Name</strong>
                        <span><?= htmlspecialchars($team['member2Name'] ?: 'N/A') ?></span>
                    </div>
                    <div class="member-info">
                        <strong>Email</strong>
                        <span><?= htmlspecialchars($team['member2Email'] ?: 'N/A') ?></span>
                    </div>
                    <div class="member-info">
                        <strong>GitHub</strong>
                        <?php if ($team['member2Github']): ?>
                            <a class="github-link" href="<?= htmlspecialchars($team['member2Github']) ?>" target="_blank" rel="noopener noreferrer">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0C5.372 0 0 5.372 0 12c0 5.302 3.438 9.8 8.205 11.385.6.111.82-.261.82-.58 0-.287-.011-1.244-.017-2.446-3.338.726-4.042-1.61-4.042-1.61-.546-1.388-1.333-1.757-1.333-1.757-1.09-.745.083-.73.083-.73 1.205.084 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.775.42-1.305.763-1.605-2.665-.303-5.467-1.332-5.467-5.931 0-1.31.469-2.381 1.235-3.221-.124-.303-.536-1.522.117-3.176 0 0 1.008-.322 3.301 1.23a11.52 11.52 0 0 1 3.003-.404c1.02.005 2.045.137 3.003.404 2.291-1.552 3.297-1.23 3.297-1.23.655 1.654.243 2.873.12 3.176.77.84 1.234 1.91 1.234 3.22 0 4.61-2.807 5.625-5.48 5.921.431.372.816 1.103.816 2.222 0 1.606-.015 2.9-.015 3.296 0 .322.216.698.824.58C20.565 21.796 24 17.3 24 12c0-6.628-5.372-12-12-12z"/>
                                </svg>
                                GitHub
                            </a>
                        <?php else: ?>
                            <span>N/A</span>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endif; ?>

                <!-- Member 3 card -->
                <?php if (!empty($team['member3Name']) || !empty($team['member3Email']) || !empty($team['member3Github'])): ?>
                <article class="member-card" aria-label="Team Member 3">
                    <div class="member-info">
                        <strong>Name</strong>
                        <span><?= htmlspecialchars($team['member3Name'] ?: 'N/A') ?></span>
                    </div>
                    <div class="member-info">
                        <strong>Email</strong>
                        <span><?= htmlspecialchars($team['member3Email'] ?: 'N/A') ?></span>
                    </div>
                    <div class="member-info">
                        <strong>GitHub</strong>
                        <?php if ($team['member3Github']): ?>
                            <a class="github-link" href="<?= htmlspecialchars($team['member3Github']) ?>" target="_blank" rel="noopener noreferrer">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0C5.372 0 0 5.372 0 12c0 5.302 3.438 9.8 8.205 11.385.6.111.82-.261.82-.58 0-.287-.011-1.244-.017-2.446-3.338.726-4.042-1.61-4.042-1.61-.546-1.388-1.333-1.757-1.333-1.757-1.09-.745.083-.73.083-.73 1.205.084 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.775.42-1.305.763-1.605-2.665-.303-5.467-1.332-5.467-5.931 0-1.31.469-2.381 1.235-3.221-.124-.303-.536-1.522.117-3.176 0 0 1.008-.322 3.301 1.23a11.52 11.52 0 0 1 3.003-.404c1.02.005 2.045.137 3.003.404 2.291-1.552 3.297-1.23 3.297-1.23.655 1.654.243 2.873.12 3.176.77.84 1.234 1.91 1.234 3.22 0 4.61-2.807 5.625-5.48 5.921.431.372.816 1.103.816 2.222 0 1.606-.015 2.9-.015 3.296 0 .322.216.698.824.58C20.565 21.796 24 17.3 24 12c0-6.628-5.372-12-12-12z"/>
                                </svg>
                                GitHub
                            </a>
                        <?php else: ?>
                            <span>N/A</span>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endif; ?>
                <?php if (!empty($team['member4Name']) || !empty($team['member4Email']) || !empty($team['member4Github'])): ?>
                <article class="member-card" aria-label="Team Member 4">
                    <div class="member-info">
                        <strong>Name</strong>
                        <span><?= htmlspecialchars($team['member4Name'] ?: 'N/A') ?></span>
                    </div>
                    <div class="member-info">
                        <strong>Email</strong>
                        <span><?= htmlspecialchars($team['member4Email'] ?: 'N/A') ?></span>
                    </div>
                    <div class="member-info">
                        <strong>GitHub</strong>
                        <?php if ($team['member4Github']): ?>
                            <a class="github-link" href="<?= htmlspecialchars($team['member4Github']) ?>" target="_blank" rel="noopener noreferrer">
                                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0C5.372 0 0 5.372 0 12c0 5.302 3.438 9.8 8.205 11.385.6.111.82-.261.82-.58 0-.287-.011-1.244-.017-2.446-3.338.726-4.042-1.61-4.042-1.61-.546-1.388-1.333-1.757-1.333-1.757-1.09-.745.083-.73.083-.73 1.205.084 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.775.42-1.305.763-1.605-2.665-.303-5.467-1.332-5.467-5.931 0-1.31.469-2.381 1.235-3.221-.124-.303-.536-1.522.117-3.176 0 0 1.008-.322 3.301 1.23a11.52 11.52 0 0 1 3.003-.404c1.02.005 2.045.137 3.003.404 2.291-1.552 3.297-1.23 3.297-1.23.655 1.654.243 2.873.12 3.176.77.84 1.234 1.91 1.234 3.22 0 4.61-2.807 5.625-5.48 5.921.431.372.816 1.103.816 2.222 0 1.606-.015 2.9-.015 3.296 0 .322.216.698.824.58C20.565 21.796 24 17.3 24 12c0-6.628-5.372-12-12-12z"/>
                                </svg>
                                GitHub
                            </a>
                        <?php else: ?>
                            <span>N/A</span>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endif; ?>
<?php if (!empty($team['projectName']) && !empty($team['projectRepo'])): ?>
<div class="project-repo-card">
    <strong class="block text-lg text-white mb-1">Project:</strong>
    <a class="text-blue-400 underline hover:text-blue-300 transition-all duration-200"
       href="<?= htmlspecialchars($team['projectRepo']) ?>"
       target="_blank" rel="noopener noreferrer">
        <?= htmlspecialchars($team['projectName']) ?>
    </a>
</div>
<?php endif; ?>
            </div>
            
            
            

<?php if (!empty($team['teamName']) && !empty($team['projectRepo'])): ?>
<div class="project-repo-card">
    <strong class="block text-lg text-white mb-1">Project:</strong>
    <a class="text-blue-400 underline hover:text-blue-300 transition-all duration-200"
       href="<?= htmlspecialchars($team['projectRepo']) ?>"
       target="_blank" rel="noopener noreferrer">
        <?= htmlspecialchars($team['teamName']) ?>
    </a>
</div>
<?php endif; ?>


            <div class="team-footer" style="margin-top: 1rem;">
                <strong>Registered On:</strong> <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($team['created_at']))) ?><br/>
                <strong>Status:</strong> <?= ucfirst($status) ?>
            </div>
        </section>

        <?php endforeach; ?>
    </div>
</body>
</html>

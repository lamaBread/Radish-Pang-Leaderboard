<?php
require_once 'db_connect.php';

// 리더보드 데이터 가져오기
$sql = "SELECT * FROM scores ORDER BY score DESC";
$result = $conn->query($sql);

// 결과 처리를 위한 배열 준비
$scores = [];
if ($result !== false) {
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $scores[] = $row;
    }
}

// 새로고침 간격 (config.php에서 정의됨)
$refreshInterval = defined('REFRESH_INTERVAL') ? REFRESH_INTERVAL : 30;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>래디쉬팡 리더보드</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">        
        <h1><a class="underline-delete" href="./setup.php"><img id="headline-img" src="./headline.png" alt=""></a></h1>

        <div class="leaderboard-container">
            <table class="leaderboard">
                <thead>
                    <tr>
                        <th id="table-head" colspan="3">Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($scores) > 0) {
                        $rank = 1;
                        foreach ($scores as $row) {
                            echo "<tr>";
                            echo "<td class='rank'>" . $rank . "등</td>";
                            echo "<td class='player'>" . htmlspecialchars($row["player_name"]) . "</td>";
                            echo "<td class='score'>" . htmlspecialchars($row["score"]) . "</td>";
                            echo "</tr>";
                            // echo "<tr class='separator'><td colspan='3'>...</td></tr>";
                            $rank++;
                        }
                    } else {
                        echo "<tr><td colspan='3' class='no-records'>아직 기록이 없습니다.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="toggle-container">
            <button class="toggle-button" id="toggleSubmitForm">수동으로 점수 제출</button>
            <div class="submit-score" id="submitScoreForm" style="display: none;">
                <h2>내 점수 제출하기</h2>
                <form action="submit_score.php" method="post">
                    <div class="form-group">
                        <label for="player_name">이름:</label>
                        <input type="text" id="player_name" name="player_name" required>
                    </div>
                    <div class="form-group">
                        <label for="score">점수:</label>
                        <input type="number" id="score" name="score" required>
                    </div>
                    <button type="submit">점수 제출</button>
                </form>
            </div>
        </div>
        
        <!-- 자동 새로고침 상태 표시 -->
        <div class="refresh-status">
            <span id="refresh-timer"><?php echo $refreshInterval; ?></span>초 후 자동 새로고침
            <button id="toggle-refresh" class="small-button">중지</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 토글 폼 기능
            const toggleButton = document.getElementById('toggleSubmitForm');
            const submitForm = document.getElementById('submitScoreForm');
            
            toggleButton.addEventListener('click', function() {
                if (submitForm.style.display === 'none') {
                    submitForm.style.display = 'block';
                    toggleButton.textContent = '제출 폼 접기';
                } else {
                    submitForm.style.display = 'none';
                    toggleButton.textContent = '수동으로 점수 제출';
                }
            });
            
            // 자동 새로고침 기능
            const refreshInterval = <?php echo $refreshInterval; ?>; // PHP에서 설정한 간격
            let remainingTime = refreshInterval;
            let refreshTimer = document.getElementById('refresh-timer');
            let toggleRefresh = document.getElementById('toggle-refresh');
            let timerId;
            
            // 타이머 시작 함수
            function startTimer() {
                // 이전 타이머가 있으면 중지
                if (timerId) {
                    clearInterval(timerId);
                }
                
                // 타이머 초기화
                remainingTime = refreshInterval;
                refreshTimer.textContent = remainingTime;
                
                // 타이머 시작
                timerId = setInterval(function() {
                    remainingTime--;
                    refreshTimer.textContent = remainingTime;
                    
                    if (remainingTime <= 0) {
                        // 시간이 다 되면 페이지 새로고침
                        window.location.reload();
                    }
                }, 1000);
            }
            
            // 타이머 중지 함수
            function stopTimer() {
                clearInterval(timerId);
                timerId = null;
            }
            
            // 새로고침 토글 버튼 이벤트
            toggleRefresh.addEventListener('click', function() {
                if (timerId) {
                    // 타이머 중지
                    stopTimer();
                    toggleRefresh.textContent = '시작';
                    refreshTimer.parentElement.classList.add('paused');
                } else {
                    // 타이머 시작
                    startTimer();
                    toggleRefresh.textContent = '중지';
                    refreshTimer.parentElement.classList.remove('paused');
                }
            });
            
            // 페이지 로드시 타이머 시작
            startTimer();
        });
    </script>
</body>
</html>
<?php
// 연결 닫기
$conn->close();
?>

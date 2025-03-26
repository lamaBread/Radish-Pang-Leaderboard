<?php
require_once 'db_connect.php';

// POST 요청 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 입력값 검증
    $player_name = trim($_POST["player_name"]);
    $score = intval($_POST["score"]);
    
    // 기본 검증
    if (empty($player_name) || $score <= 0) {
        die("올바른 이름과 점수를 입력해주세요.");
    }
    
    try {
        // SQLite용 prepared statement
        $stmt = $conn->prepare("INSERT INTO scores (player_name, score) VALUES (:player_name, :score)");
        $stmt->bindValue(':player_name', $player_name, SQLITE3_TEXT);
        $stmt->bindValue(':score', $score, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        // 성공적으로 저장됨
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        // 오류 발생 시 로그 기록 후 메인 페이지로 리다이렉트
        error_log("Error in submit_score.php: " . $e->getMessage());
        header("Location: index.php");
        exit();
    }
}

// 연결 닫기
$conn->close();
?>

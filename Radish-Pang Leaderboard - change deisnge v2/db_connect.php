<?php
require_once 'config.php';

// SQLite 데이터베이스 연결 생성
try {
    $conn = new SQLite3(DB_FILE);
    
    // 외래키 제약 조건 활성화
    $conn->exec('PRAGMA foreign_keys = ON');
    
    // 데이터베이스 파일이 존재하지만 테이블이 없을 수 있으므로 테이블 확인
    $tableCheck = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='scores'");
    if (!$tableCheck || $tableCheck->fetchArray() === false) {
        // 테이블이 없으면 생성
        $sql = "CREATE TABLE IF NOT EXISTS scores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT NOT NULL,
            score INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql);
    }
    
} catch (Exception $e) {
    die("데이터베이스 연결 실패: " . $e->getMessage());
}
?>

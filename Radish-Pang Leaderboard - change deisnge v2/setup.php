<?php
require_once 'config.php';

// 레코드 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_records'])) {
    if (isset($_POST['selected_records']) && is_array($_POST['selected_records'])) {
        try {
            $conn = new SQLite3(DB_FILE);
            
            foreach ($_POST['selected_records'] as $record_id) {
                $stmt = $conn->prepare("DELETE FROM scores WHERE id = :id");
                $stmt->bindValue(':id', intval($record_id), SQLITE3_INTEGER);
                $stmt->execute();
            }
            
            $deleted_count = count($_POST['selected_records']);
            $message = "{$deleted_count}개의 기록이 삭제되었습니다.";
            $conn->close();
        } catch (Exception $e) {
            $error = "삭제 중 오류가 발생했습니다: " . $e->getMessage();
        }
    }
}

// 데이터베이스 초기화 처리
if (file_exists(DB_FILE)) {
    // 이미 존재하는 경우 백업 생성 여부 묻기
    if (isset($_GET['overwrite']) && $_GET['overwrite'] == 'true') {
        unlink(DB_FILE);
        $db_initialized = false;
    } else {
        $db_initialized = true;
    }
} else {
    $db_initialized = false;
}

// 데이터베이스 초기화
if (!$db_initialized && !isset($_GET['view_records'])) {
    try {
        // 새 SQLite 데이터베이스 생성
        $conn = new SQLite3(DB_FILE);
        
        // 외래키 제약 조건 활성화
        $conn->exec('PRAGMA foreign_keys = ON');
        
        // 테이블 생성
        $sql = "CREATE TABLE IF NOT EXISTS scores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT NOT NULL,
            score INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $result = $conn->exec($sql);
        if (!$result) {
            throw new Exception($conn->lastErrorMsg());
        }
        
        $success_message = "SQLite 데이터베이스 설정이 완료되었습니다.";
        
        // 연결 닫기
        $conn->close();
        
    } catch (Exception $e) {
        $error = "데이터베이스 설정 실패: " . $e->getMessage();
    }
}

// 모든 레코드 가져오기
$records = [];
if (file_exists(DB_FILE)) {
    try {
        $conn = new SQLite3(DB_FILE);
        $result = $conn->query("SELECT * FROM scores ORDER BY score DESC");
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $records[] = $row;
        }
        
        $conn->close();
    } catch (Exception $e) {
        $error = "데이터베이스 조회 실패: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>데이터베이스 설정</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .button-group {
            margin: 20px 0;
        }
        .button-group a, .button-group button {
            display: inline-block;
            margin-right: 10px;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .button-group a.danger, .button-group button.danger {
            background-color: #f44336;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .checkbox-column {
            width: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>데이터베이스 설정</h1>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="button-group">
            <a href="index.php">메인 페이지로</a>
            
            <?php if ($db_initialized && !isset($_GET['view_records'])): ?>
                <a href="setup.php?view_records=true">기록 관리하기</a>
                <a href="setup.php?overwrite=true" class="danger" onclick="return confirm('정말로 데이터베이스를 초기화하시겠습니까? 모든 데이터가 삭제됩니다.');">데이터베이스 초기화</a>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_GET['view_records']) && count($records) > 0): ?>
            <h2>저장된 기록 관리</h2>
            <form method="post">
                <table>
                    <thead>
                        <tr>
                            <th class="checkbox-column">선택</th>
                            <th>ID</th>
                            <th>플레이어</th>
                            <th>점수</th>
                            <th>기록 시간</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td class="checkbox-column">
                                    <input type="checkbox" name="selected_records[]" value="<?php echo $record['id']; ?>">
                                </td>
                                <td><?php echo $record['id']; ?></td>
                                <td><?php echo htmlspecialchars($record['player_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['score']); ?></td>
                                <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="button-group">
                    <button type="submit" name="delete_records" class="danger" onclick="return confirm('선택한 기록을 삭제하시겠습니까?');">선택한 기록 삭제</button>
                    <a href="setup.php">뒤로</a>
                </div>
            </form>
        <?php elseif (isset($_GET['view_records'])): ?>
            <p>저장된 기록이 없습니다.</p>
            <div class="button-group">
                <a href="setup.php">뒤로</a>
            </div>
        <?php endif; ?>
        
        <?php if (!isset($_GET['view_records']) && file_exists(DB_FILE)): ?>
            <p>
                <?php if (count($records) > 0): ?>
                    데이터베이스에 <?php echo count($records); ?>개의 기록이 저장되어 있습니다.
                <?php else: ?>
                    데이터베이스가 설정되었지만 저장된 기록이 없습니다.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>

# Leaderboard Webpage Guide

본 시스템은 플레이어 이름과 점수를 기록하여 표 형태로 표현하는 웹 페이지이다.    
화면에서 수동으로 점수와 이름을 입력할 수 있다.    
POST 요청을 처리하는 API를 제공한다. (/submit_score.php)    

한편 SQLite 를 사용하므로, DB 파일이 존재한다. DB 파일 이름의 기본 설정 값은 leaderboard.sqlite 이다.

동명이인의 경우 제한 없이 입력받는다.

설정 페이지로의 진입은, 상단의 '래디쉬팡' 그림을 클릭하면 된다. 해당 페이지에서 기록의 삭제, DB 초기화를 수행할 수 있다.


### 실행 방법 1 (for Mac)

쉬운 실행을 위해, 내가 웹페이지를 앱으로 빌드했다. 게임 환경이 맥이므로, 맥 용으로 빌드했다.

해당 앱을 실행하는 즉시, localhost:18000 으로 서버가 실행되고, 해당 페이지가 출력되는 앱이 실행된다.


### 실행 방법 2 (for any terminal)

PHP를 설치하고 다음 명령어를 입력하여 웹서버를 실행한다.    
명령어는 index.php 가 있는 폴더에서 실행한다.    
index.php는 /www 내부에 있다.

```bash
php -S localhost:49694 -t ./
```


### 실행 방법 3 (for Windows)

"Radish-Pang Leaderboard.exe" 를 실행한다.
전체화면으로 실행되며, 해제할 수 없다.
Alt + F4 로 종료할 것.


### DB 초기화

웹 페이지 상단의 "래디쉬-팡!" 글씨를 클릭하면 DB 초기화 화면으로 진입한다.
DB를 초기화 할 것인지 1회 더 물어본다. 실수로 초기화 하지 않도록 주의할 것.

### 주요 파일 설명

#### 외부 접근 가능 목록
- index.php             <- 리더보드 페이지
- setup.php             <- DB 초기화 페이지 & DB 수정
- submit_score.php      <- POST API (스코어 등록) 

#### 시스템 내부적으로 사용하는 모듈 목록
- db_connect.php        <- DB 연결 모듈
- config.php            <- DB 파일 경로 설정 & 리더보드 새로고침 간격 설정
- leaderboard.sqlite    <- DB 파일
- css/style.css         <- CSS



     
# cURL Guide

```bash
curl -d "player_name=POST API TEST 2&score=1000" -X POST http://localhost:18000/submit_score.php
```
위의 실행문으로 POST 요청이 정상 처리됨을 검증했다.

     
# Unity Submission Guide

target: "http://localhost:18000/submit_score.php"

위의 주소로 다음 두 개의 변수를 갖는 POST 요청을 보내면 된다.

- player_name
    - 문자열로 보낼 것.
- score
    - 정수로 보낼 것.

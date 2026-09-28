-- MariaDB 5.5.50 / MySQL 5.5 compatible. Run 01_create first.
-- Existing administrator edits are never overwritten on rerun.
SET NAMES utf8mb4;
START TRANSACTION;

-- main / 첫 화면: We shape
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_01', CONVERT(0x5765207368617065 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_01');

-- main / 첫 화면: SCENE
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_02', CONVERT(0x5343454e45 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_02');

-- main / 첫 화면: 경이로운 장면을 만들고
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_03', CONVERT(0xeab2bdec9db4eba19cec9ab420ec9ea5eba9b4ec9d8420eba78ceb93a4eab3a0 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_03');

-- main / 첫 화면: We shape
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_04', CONVERT(0x5765207368617065 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_04');

-- main / 첫 화면: EXPERIENCE
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_05', CONVERT(0x455850455249454e4345 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_05');

-- main / 첫 화면: 관계맺는 경험을 빚어
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_06', CONVERT(0xeab480eab384eba7baeb8a9420eab2bded9798ec9d8420ebb99aec96b4 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_06');

-- main / 첫 화면: We shape
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_07', CONVERT(0x5765207368617065 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_07');

-- main / 첫 화면: WEEKENDS
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_08', CONVERT(0x5745454b454e4453 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_08');

-- main / 첫 화면: 잊지 못할 주말을 선물합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_09', CONVERT(0xec9e8aeca78020ebaabbed95a020eca3bceba790ec9d8420ec84a0ebacbced95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_09');

-- main / 첫 화면: 오프라인 이벤트의 피크는 주말.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_10', CONVERT(0xec98a4ed9484eb9dbcec9db820ec9db4ebb2a4ed8ab8ec9d9820ed94bced81aceb8a9420eca3bceba7902e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_10');

-- main / 첫 화면: 현장과 사무실을 지키는 일이 잦았습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_11', CONVERT(0xed9884ec9ea5eab3bc20ec82acebacb4ec8ba4ec9d8420eca780ed82a4eb8a9420ec9dbcec9db420ec9ea6ec9598ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_11');

-- main / 첫 화면: 어느새 주말은,
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_12', CONVERT(0xec96b4eb8a90ec838820eca3bceba790ec9d802c USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_12');

-- main / 첫 화면: 우리의 노력과 결실을 의미하는 정체성이 되었습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_13', CONVERT(0xec9ab0eba6acec9d9820eb85b8eba0a5eab3bc20eab2b0ec8ba4ec9d8420ec9d98ebafb8ed9598eb8a9420eca095ecb2b4ec84b1ec9db420eb9098ec9788ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_13');

-- main / 첫 화면: 안녕하세요.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_14', CONVERT(0xec9588eb8595ed9598ec84b8ec9a942e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_14');

-- main / 첫 화면: 주말을 만드는 사람들, 위켄즈입니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_15', CONVERT(0xeca3bceba790ec9d8420eba78ceb939ceb8a9420ec82aceb9e8ceb93a42c20ec9c84ecbc84eca688ec9e85eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_15');

-- main / 첫 화면: 우리가 가장 잘 하는 팝업 익스피리언스를 매개로
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_16', CONVERT(0xec9ab0eba6aceab08020eab080ec9ea520ec9e9820ed9598eb8a9420ed8c9dec978520ec9db5ec8aa4ed94bceba6acec96b8ec8aa4eba5bc20eba7a4eab09ceba19c USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_16');

-- main / 첫 화면: 브랜드와 고객이 가장 가까운 거리에서 만나 관계맺도록 돕고
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_17', CONVERT(0xebb88ceb9e9ceb939cec998020eab3a0eab09dec9db420eab080ec9ea520eab080eab98cec9ab420eab1b0eba6acec9790ec849c20eba78ceb829820eab480eab384eba7baeb8f84eba19d20eb8f95eab3a0 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_17');

-- main / 첫 화면: 행복한 주말처럼 오롯이 기억되는 순간과 경험을 빚습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'visual_18', CONVERT(0xed9689ebb3b5ed959c20eca3bceba790ecb298eb9fbc20ec98a4eba1afec9db420eab8b0ec96b5eb9098eb8a9420ec889ceab084eab3bc20eab2bded9798ec9d8420ebb99aec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'visual_18');

-- main / 사진 갤러리: Weekends | 위켄즈
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'gallery_01', CONVERT(0x5765656b656e6473207c20ec9c84ecbc84eca688 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'gallery_01');

-- main / 성과: What We've
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_01', CONVERT(0x57686174205765277665 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_01');

-- main / 성과: What
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_02', CONVERT(0x57686174 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_02');

-- main / 성과: We've
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_03', CONVERT(0x5765277665 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_03');

-- main / 성과: Achieved
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_04', CONVERT(0x4163686965766564 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_04');

-- main / 성과: 기업신용등급
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_05', CONVERT(0xeab8b0ec9785ec8ba0ec9aa9eb93b1eab889 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_05');

-- main / 성과: (2025)
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_06', CONVERT(0x283230323529 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_06');

-- main / 성과: 한국기술신용평가
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_07', CONVERT(0xed959ceab5adeab8b0ec88a0ec8ba0ec9aa9ed8f89eab080 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_07');

-- main / 성과: KB국민은행
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_08', CONVERT(0x4b42eab5adebafbcec9d80ed9689 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_08');

-- main / 성과: A
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_09', CONVERT(0x41 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_09');

-- main / 성과: 파트너사
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_10', CONVERT(0xed8c8ced8ab8eb8488ec82ac USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_10');

-- main / 성과: 2025
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_11', CONVERT(0x32303235 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_11');

-- main / 성과: 150+
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_12', CONVERT(0x3135302b USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_12');

-- main / 성과: 현장 참가자
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_13', CONVERT(0xed9884ec9ea520ecb0b8eab080ec9e90 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_13');

-- main / 성과: 2025
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_14', CONVERT(0x32303235 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_14');

-- main / 성과: 600K+
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'achieve_15', CONVERT(0x3630304b2b USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'achieve_15');

-- main / 서비스 소개 · 오프라인 경험: Off-line
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_circle_01', CONVERT(0x4f66662d6c696e65 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_circle_01');

-- main / 서비스 소개 · 오프라인 경험: Experience+
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_circle_02', CONVERT(0x457870657269656e63652b USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_circle_02');

-- main / 서비스 소개 · 오프라인 경험: 팝업 익스피리언스를 필두로 모든
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_circle_03', CONVERT(0xed8c9dec978520ec9db5ec8aa4ed94bceba6acec96b8ec8aa4eba5bc20ed9584eb9190eba19c20ebaaa8eb93a0 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_circle_03');

-- main / 서비스 소개 · 오프라인 경험: 오프라인 경험을 제공합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_circle_04', CONVERT(0xec98a4ed9484eb9dbcec9db820eab2bded9798ec9d8420eca09ceab3b5ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_circle_04');

-- main / 서비스 소개 · 오프라인 경험: 기획부터 실행까지, 한 창구에서 모두 가능합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_circle_05', CONVERT(0xeab8b0ed9a8debb680ed84b020ec8ba4ed9689eab98ceca7802c20ed959c20ecb0bdeab5acec9790ec849c20ebaaa8eb919020eab080eb8aa5ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_circle_05');

-- main / 서비스 소개 · 오프라인 경험: OOH, 페이드 미디어 등을 더해 더 넓은 접근까지 가능합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_circle_06', CONVERT(0x4f4f482c20ed8e98ec9db4eb939c20ebafb8eb9494ec96b420eb93b1ec9d8420eb8d94ed95b420eb8d9420eb8493ec9d8020eca091eab7bceab98ceca78020eab080eb8aa5ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_circle_06');

-- main / 서비스 소개 · 디자인: Design
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_triangle_01', CONVERT(0x44657369676e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_triangle_01');

-- main / 서비스 소개 · 디자인: 평면에서 공간에 이르는 디자인
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_triangle_02', CONVERT(0xed8f89eba9b4ec9790ec849c20eab3b5eab084ec979020ec9db4eba5b4eb8a9420eb9494ec9e90ec9db8 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_triangle_02');

-- main / 서비스 소개 · 디자인: 일체를 제공합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_triangle_03', CONVERT(0xec9dbcecb2b4eba5bc20eca09ceab3b5ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_triangle_03');

-- main / 서비스 소개 · 디자인: 결과물의 핵심이 되는 디자인. 모든 아이디어를 시각화하고
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_triangle_04', CONVERT(0xeab2b0eab3bcebacbcec9d9820ed95b5ec8bacec9db420eb9098eb8a9420eb9494ec9e90ec9db82e20ebaaa8eb93a020ec9584ec9db4eb9494ec96b4eba5bc20ec8b9ceab081ed9994ed9598eab3a0 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_triangle_04');

-- main / 서비스 소개 · 디자인: 제작, 감리, 실행까지 주도하여 직접 완성합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_triangle_05', CONVERT(0xeca09cec9e912c20eab090eba6ac2c20ec8ba4ed9689eab98ceca78020eca3bceb8f84ed9598ec97ac20eca781eca09120ec9984ec84b1ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_triangle_05');

-- main / 서비스 소개 · PR / 바이럴: PR, Viral
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_square_01', CONVERT(0x50522c20566972616c USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_square_01');

-- main / 서비스 소개 · PR / 바이럴: 좋은 이벤트를 꾸리는 것 만큼
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_square_02', CONVERT(0xeca28bec9d8020ec9db4ebb2a4ed8ab8eba5bc20eabeb8eba6aceb8a9420eab28320eba78ced81bc USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_square_02');

-- main / 서비스 소개 · PR / 바이럴: 알리는 활동의 중요도가 높아지고
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_square_03', CONVERT(0xec958ceba6aceb8a9420ed999ceb8f99ec9d9820eca491ec9a94eb8f84eab08020eb8692ec9584eca780eab3a0 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_square_03');

-- main / 서비스 소개 · PR / 바이럴: 있습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_square_04', CONVERT(0xec9e88ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_square_04');

-- main / 서비스 소개 · PR / 바이럴: 최적화된 스피커를 통해 멀리, 많이 알려드립니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'services_square_05', CONVERT(0xecb59ceca081ed9994eb909c20ec8aa4ed94bcecbba4eba5bc20ed86b5ed95b420eba980eba6ac2c20eba78eec9db420ec958ceba0a4eb939ceba6bdeb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'services_square_05');

-- main / 사진 갤러리 흐르는 문구: THE PEOPLE BEHIND WEEKENDS
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'main', 'gallery_marquee', CONVERT(0x5448452050454f504c4520424548494e44205745454b454e4453 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'main' AND content_key = 'gallery_marquee');

-- about / 떨어지는 글자: 떨어지는 글자 문구 (공백 제외 최대 40자)
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'falling_text', CONVERT(0xeab7b8eb9e98eb8f84eb82b4ec9dbcec9d80ec98b5eb8b88eb8ba4 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'falling_text');

-- about / 첫 화면: Weekends
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'visual_01', CONVERT(0x5765656b656e6473 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'visual_01');

-- about / 첫 화면: 모두가 오프라인 이벤트는 이제 죽었다고 말했던 때도 있었습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'visual_02', CONVERT(0xebaaa8eb9190eab08020ec98a4ed9484eb9dbcec9db820ec9db4ebb2a4ed8ab8eb8a9420ec9db4eca09c20eca3bdec9788eb8ba4eab3a020eba790ed9688eb8d9820eb958ceb8f8420ec9e88ec9788ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'visual_02');

-- about / 첫 화면: 하지만 세상이 단절되었던 2015년 메르스, 2020년 코비드도 이겨냈습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'visual_03', CONVERT(0xed9598eca780eba78c20ec84b8ec8381ec9db420eb8ba8eca088eb9098ec9788eb8d982032303135eb858420eba994eba5b4ec8aa42c2032303230eb858420ecbd94ebb984eb939ceb8f8420ec9db4eab2a8eb8388ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'visual_03');

-- about / 첫 화면: 또 다시 새로운 국면, AI 시대를 맞이합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'visual_04', CONVERT(0xeb989020eb8ba4ec8b9c20ec8388eba19cec9ab420eab5adeba9b42c20414920ec8b9ceb8c80eba5bc20eba79eec9db4ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'visual_04');

-- about / 첫 화면: 발전하는 기술을 주도적으로 활용하여 우리가 자랑하는 실체 있는 경험으로, 앞으로도 우리는 승리할것입
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'visual_05', CONVERT(0xebb09ceca084ed9598eb8a9420eab8b0ec88a0ec9d8420eca3bceb8f84eca081ec9cbceba19c20ed999cec9aa9ed9598ec97ac20ec9ab0eba6aceab08020ec9e90eb9e91ed9598eb8a9420ec8ba4ecb2b420ec9e88eb8a9420eab2bded9798ec9cbceba19c2c20ec959eec9cbceba19ceb8f8420ec9ab0eba6aceb8a9420ec8ab9eba6aced95a0eab283ec9e85eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'visual_05');

-- about / 첫 화면: 여전히 휴먼 터치는 유효합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'visual_06', CONVERT(0xec97aceca084ed9e8820ed9cb4eba8bc20ed84b0ecb998eb8a9420ec9ca0ed9aa8ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'visual_06');

-- about / 그라데이션: Weekends
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'gradient_01', CONVERT(0x5765656b656e6473 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'gradient_01');

-- about / 브랜드 이야기: 진짜를
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_01', CONVERT(0xeca784eca79ceba5bc USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_01');

-- about / 브랜드 이야기: 보여드리죠.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_02', CONVERT(0xebb3b4ec97aceb939ceba6aceca3a02e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_02');

-- about / 브랜드 이야기: 브랜드를 만나면 우리는 그 즉시 그 브랜드의 오타쿠가 됩니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_03', CONVERT(0xebb88ceb9e9ceb939ceba5bc20eba78ceb8298eba9b420ec9ab0eba6aceb8a9420eab7b820eca689ec8b9c20eab7b820ebb88ceb9e9ceb939cec9d9820ec98a4ed8380ecbfa0eab08020eb90a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_03');

-- about / 브랜드 이야기: N시간의 플레이타임과 뷰잉타임, N끼니의 식사 경험, N만원의 소비 경험.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_04', CONVERT(0x4eec8b9ceab084ec9d9820ed948ceba088ec9db4ed8380ec9e84eab3bc20ebb7b0ec9e89ed8380ec9e842c204eeb81bceb8b88ec9d9820ec8b9dec82ac20eab2bded97982c204eeba78cec9b90ec9d9820ec868cebb98420eab2bded97982e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_04');

-- about / 브랜드 이야기: 직장인이 아닌 직업인을 지향하는 우리의 킥-오프.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_05', CONVERT(0xeca781ec9ea5ec9db8ec9db420ec9584eb8b8c20eca781ec9785ec9db8ec9d8420eca780ed96a5ed9598eb8a9420ec9ab0eba6acec9d9820ed82a52dec98a4ed94842e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_05');

-- about / 브랜드 이야기: 애정 없이 일을 못해요. 하니 남다른 디테일과 수준이 나옵니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_06', CONVERT(0xec95a0eca09520ec9786ec9db420ec9dbcec9d8420ebaabbed95b4ec9a942e20ed9598eb8b8820eb82a8eb8ba4eba5b820eb9494ed858cec9dbceab3bc20ec8898eca480ec9db420eb8298ec98b5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_06');

-- about / 브랜드 이야기: 위켄즈의 킥-오프는 언제나 브랜드를 향한 애정과 존중입니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_07', CONVERT(0xec9c84ecbc84eca688ec9d9820ed82a52dec98a4ed9484eb8a9420ec96b8eca09ceb829820ebb88ceb9e9ceb939ceba5bc20ed96a5ed959c20ec95a0eca095eab3bc20eca1b4eca491ec9e85eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_07');

-- about / 브랜드 이야기: 원피스를
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_08', CONVERT(0xec9b90ed94bcec8aa4eba5bc USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_08');

-- about / 브랜드 이야기: 찾아
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_09', CONVERT(0xecb0beec9584 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_09');

-- about / 브랜드 이야기: 항해하는
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_10', CONVERT(0xed95aded95b4ed9598eb8a94 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_10');

-- about / 브랜드 이야기: 중입니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_11', CONVERT(0xeca491ec9e85eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_11');

-- about / 브랜드 이야기: 누구는 오올블루를 찾아서, 누구는 최고의 검사가 되기 위해,
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_12', CONVERT(0xeb8884eab5aceb8a9420ec98a4ec98acebb894eba3a8eba5bc20ecb0beec9584ec849c2c20eb8884eab5aceb8a9420ecb59ceab3a0ec9d9820eab280ec82aceab08020eb9098eab8b020ec9c84ed95b42c USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_12');

-- about / 브랜드 이야기: 또 누구는 언제나 원했던 것이어서.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_13', CONVERT(0xeb989020eb8884eab5aceb8a9420ec96b8eca09ceb829820ec9b90ed9688eb8d9820eab283ec9db4ec96b4ec849c2e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_13');

-- about / 브랜드 이야기: 목적이 달라도 함께 원피스를 찾아가는 이야기가 너무 좋았어요.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_14', CONVERT(0xebaaa9eca081ec9db420eb8baceb9dbceb8f8420ed95a8eabb9820ec9b90ed94bcec8aa4eba5bc20ecb0beec9584eab080eb8a9420ec9db4ec95bceab8b0eab08020eb8488ebacb420eca28bec9598ec96b4ec9a942e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_14');

-- about / 브랜드 이야기: 그래서 그런 회사를 만들기로 했습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_15', CONVERT(0xeab7b8eb9e98ec849c20eab7b8eb9fb020ed9a8cec82aceba5bc20eba78ceb93a4eab8b0eba19c20ed9688ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_15');

-- about / 브랜드 이야기: 임직원은 물론 고객사, 협력사와 함께, 오늘도 모험을 시작합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_16', CONVERT(0xec9e84eca781ec9b90ec9d8020ebacbceba1a020eab3a0eab09dec82ac2c20ed9891eba0a5ec82acec998020ed95a8eabb982c20ec98a4eb8a98eb8f8420ebaaa8ed9798ec9d8420ec8b9cec9e91ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_16');

-- about / 브랜드 이야기: 누구는 오올블루를 찾아서, 누구는 최고의 검사가 되기 위해,
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_17', CONVERT(0xeb8884eab5aceb8a9420ec98a4ec98acebb894eba3a8eba5bc20ecb0beec9584ec849c2c20eb8884eab5aceb8a9420ecb59ceab3a0ec9d9820eab280ec82aceab08020eb9098eab8b020ec9c84ed95b42c USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_17');

-- about / 브랜드 이야기: 또 누구는 언제나 원했던 것이어서.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_18', CONVERT(0xeb989020eb8884eab5aceb8a9420ec96b8eca09ceb829820ec9b90ed9688eb8d9820eab283ec9db4ec96b4ec849c2e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_18');

-- about / 브랜드 이야기: 목적이 달라도 함께 원피스를 찾아가는 이야기가 너무 좋았어요.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_19', CONVERT(0xebaaa9eca081ec9db420eb8baceb9dbceb8f8420ed95a8eabb9820ec9b90ed94bcec8aa4eba5bc20ecb0beec9584eab080eb8a9420ec9db4ec95bceab8b0eab08020eb8488ebacb420eca28bec9598ec96b4ec9a942e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_19');

-- about / 브랜드 이야기: 그래서 그런 회사를 만들기로 했습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_20', CONVERT(0xeab7b8eb9e98ec849c20eab7b8eb9fb020ed9a8cec82aceba5bc20eba78ceb93a4eab8b0eba19c20ed9688ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_20');

-- about / 브랜드 이야기: 임직원은 물론 고객사, 협력사와 함께, 오늘도 모험을 시작합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'thought_21', CONVERT(0xec9e84eca781ec9b90ec9d8020ebacbceba1a020eab3a0eab09dec82ac2c20ed9891eba0a5ec82acec998020ed95a8eabb982c20ec98a4eb8a98eb8f8420ebaaa8ed9798ec9d8420ec8b9cec9e91ed95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'thought_21');

-- about / 파트너 · 팀 소개: Creating
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_01', CONVERT(0x4372656174696e67 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_01');

-- about / 파트너 · 팀 소개: Experiences
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_02', CONVERT(0x457870657269656e636573 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_02');

-- about / 파트너 · 팀 소개: Together.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_03', CONVERT(0x546f6765746865722e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_03');

-- about / 파트너 · 팀 소개: 다양한 브랜드와 함께 사람들의 기억에 오래 남을
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_04', CONVERT(0xeb8ba4ec9691ed959c20ebb88ceb9e9ceb939cec998020ed95a8eabb9820ec82aceb9e8ceb93a4ec9d9820eab8b0ec96b5ec979020ec98a4eb9e9820eb82a8ec9d84 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_04');

-- about / 파트너 · 팀 소개: 공간과 경험을 만들어가고 있습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_05', CONVERT(0xeab3b5eab084eab3bc20eab2bded9798ec9d8420eba78ceb93a4ec96b4eab080eab3a020ec9e88ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_05');

-- about / 파트너 · 팀 소개: Our Team
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_06', CONVERT(0x4f7572205465616d USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_06');

-- about / 파트너 · 팀 소개: 아쉽게도?! 엄마/아빠보다, 남편/아내보다, 자녀보다 더 많은 시간을 동료와 함께 보내게 됩니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_07', CONVERT(0xec9584ec89bdeab28ceb8f843f2120ec9784eba7882fec9584ebb9a0ebb3b4eb8ba42c20eb82a8ed8eb82fec9584eb82b4ebb3b4eb8ba42c20ec9e90eb8580ebb3b4eb8ba420eb8d9420eba78eec9d8020ec8b9ceab084ec9d8420eb8f99eba38cec998020ed95a8eabb9820ebb3b4eb82b4eab28c20eb90a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_07');

-- about / 파트너 · 팀 소개: 의리와 온정은 필수입니다. 험난한 세상을 함께 헤쳐나갈 수 있는 서로에게 좋은 동료가 됩시다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_08', CONVERT(0xec9d98eba6acec998020ec98a8eca095ec9d8020ed9584ec8898ec9e85eb8b88eb8ba42e20ed9798eb829ced959c20ec84b8ec8381ec9d8420ed95a8eabb9820ed97a4ecb390eb8298eab08820ec889820ec9e88eb8a9420ec849ceba19cec9790eab28c20eca28bec9d8020eb8f99eba38ceab08020eb90a9ec8b9ceb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_08');

-- about / 파트너 · 팀 소개: 프로라면 프로답게, 아직 프로가 되지 못했다면 최대한 빠르게 프로가 되어야합니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_09', CONVERT(0xed9484eba19ceb9dbceba9b420ed9484eba19ceb8bb5eab28c2c20ec9584eca78120ed9484eba19ceab08020eb9098eca78020ebaabbed9688eb8ba4eba9b420ecb59ceb8c80ed959c20ebb9a0eba5b4eab28c20ed9484eba19ceab08020eb9098ec96b4ec95bced95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_09');

-- about / 파트너 · 팀 소개: 명함이 품질보증이 될 수 있도록 프로 퍼포먼스를 보여줍시다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_10', CONVERT(0xebaa85ed95a8ec9db420ed9288eca788ebb3b4eca69dec9db420eb90a020ec889820ec9e88eb8f84eba19d20ed9484eba19c20ed8dbced8faceba8bcec8aa4eba5bc20ebb3b4ec97aceca48dec8b9ceb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_10');

-- about / 파트너 · 팀 소개: 한 명도, 한 시간도 부당하게 일하지 않습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_11', CONVERT(0xed959c20ebaa85eb8f842c20ed959c20ec8b9ceab084eb8f8420ebb680eb8bb9ed9598eab28c20ec9dbced9598eca78020ec958aec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_11');

-- about / 파트너 · 팀 소개: 식대, 연/월/반차, 연장 근로 수당, 성과급, DC형 퇴직금 등 법적 기준을 완벽히 충족하는 근무
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_12', CONVERT(0xec8b9deb8c802c20ec97b02fec9b942febb098ecb0a82c20ec97b0ec9ea520eab7bceba19c20ec8898eb8bb92c20ec84b1eab3bceab8892c204443ed989520ed87b4eca781eab88820eb93b120ebb295eca08120eab8b0eca480ec9d8420ec9984ebb2bded9e8820ecb6a9eca1b1ed9598eb8a9420eab7bcebacb420eca1b0eab1b4ec9790ec849c20ec9dbced95a9eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_12');

-- about / 파트너 · 팀 소개: 창립 이래 매년 한번은 워크샵을 갑니다. 맛있는 것도 가능한 자주 먹습니다.
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_13', CONVERT(0xecb0bdeba6bd20ec9db4eb9e9820eba7a4eb858420ed959cebb288ec9d8020ec9b8ced81acec83b5ec9d8420eab091eb8b88eb8ba42e20eba79bec9e88eb8a9420eab283eb8f8420eab080eb8aa5ed959c20ec9e90eca3bc20eba8b9ec8ab5eb8b88eb8ba42e USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_13');

-- about / 파트너 · 팀 소개: 함께하는 시간이 젊은 날의 찬란한 기억이 될 수 있도록 합시다. 피할 수 없어요. 즐겨요!
INSERT INTO site_content_texts (page_key, content_key, text_value, revision, updated_at)
SELECT 'about', 'ours_14', CONVERT(0xed95a8eabb98ed9598eb8a9420ec8b9ceab084ec9db420eca08aec9d8020eb82a0ec9d9820ecb0aceb9e80ed959c20eab8b0ec96b5ec9db420eb90a020ec889820ec9e88eb8f84eba19d20ed95a9ec8b9ceb8ba42e20ed94bced95a020ec889820ec9786ec96b4ec9a942e20eca690eab2a8ec9a9421 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_texts WHERE page_key = 'about' AND content_key = 'ours_14');

-- 메인 사진 1
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_01', '/@resource/images/main/bento01.jpg', CONVERT(0x535550455243454c4c207c20ec8a88ed8dbcec8580 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_01');

-- 메인 사진 2
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_02', '/@resource/images/main/bento02.jpg', CONVERT(0x52696f742047616d6573207c20eb9dbcec9db4ec978720eab28cec9e84eca688 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_02');

-- 메인 사진 3
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_03', '/@resource/images/main/bento03.jpg', CONVERT(0x414d4f524550414349464943207c20ec9584ebaaa8eba088ed8dbcec8b9ced94bd USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_03');

-- 메인 사진 4
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_04', '/@resource/images/main/bento04.jpg', CONVERT(0x4e45584f4e207c20eb84a5ec8aa8 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_04');

-- 메인 사진 5 · 중앙 단체사진
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_05', '/@resource/images/main/bento05.jpg', CONVERT(0x5765656b656e6473207c20ec9c84ecbc84eca688 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_05');

-- 메인 사진 6
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_06', '/@resource/images/main/bento06.jpg', CONVERT(0x4b524146544f4e207c20ed81aceb9e98ed9484ed86a4 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_06');

-- 메인 사진 7
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_07', '/@resource/images/main/bento07.jpg', CONVERT(0x44455653495354455253207c20eb8db0ebb88cec8b9cec8aa4ed84b0eca688 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_07');

-- 메인 사진 8
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_08', '/@resource/images/main/bento08.jpg', CONVERT(0x4e41564552207c20eb84a4ec9db4ebb284 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_08');

-- 메인 사진 9
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'main_gallery_09', '/@resource/images/main/bento09.jpg', CONVERT(0x4b4941207c20eab8b0ec9584 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'main_gallery_09');

-- 단체사진 · PC
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'team_group_desktop', '/@resource/images/about/team-1.jpg', CONVERT(0x5765656b656e6473 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'team_group_desktop');

-- 단체사진 · 모바일
INSERT INTO site_content_images (slot_key, storage_key, alt_text, revision, updated_at)
SELECT 'team_group_mobile', '/@resource/images/about/team-1.png', CONVERT(0x5765656b656e6473 USING utf8mb4), 1, NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM site_content_images WHERE slot_key = 'team_group_mobile');

COMMIT;

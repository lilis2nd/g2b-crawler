# 나라장터 다중키워드 검색기 (G2B Multi-keyword Search Solution)
## 소개
당 솔루션은 조달청 나라장터(https://www.g2b.go.kr)의 실공고와 사전규격을 원하는 키워드로 일괄 검색하여 결과를 한 눈에 볼 수 있도록 하고자 합니다.

기존 Python 방식에 비해 별도의 파일이 저장되지 않고 웹상에서 간단히 보고 해당 공고의 직접연결 링크만 제공하고 있습니다. 실은 회사에서 매일 공고 검색을 하다보니 필요성을 느껴 슬쩍 만들었습니다.

완벽히 근본없는 프로그래밍이기 떄문에 코드가 아주아주 조악합니다.. 코드를 보시면 답답하실 수 있습니다. 더 발전시킬 수 있는 방법이 있다면 언제든 피드백 부탁드립니다.

This solution simultaneously searches real and pre orders from Republic Of Korea's [Public Procurement Service](https://www.g2b.go.kr) website using multiple keywords.

Unlike conventional Python crawlers which creates separate files to store search results, this solution only displays the results on-screen with direct links to the G2B website.

The code is very amateur and at elementary level, so I wouldn't review the code if I were you. If you have any feedbacks on improving this solution in any way, please feel free to contribute.

## 요구사항 (Requirements)
* PHP 7.2
* 웹서버 (Webserver)
* 공공데이터포탈(https://www.data.go.kr/) OpenAPI 인증키 (Open Data Portal OpenAPI public key)

## 사용법 (Usage)
1. 이 저장소를 클론하고 웹서버에 연결합니다.
2. `assets` 폴더 안에 `servicekey.php` 파일을 만들고 아래의 코드를 넣습니다.
    ```php
    // service.php
    <?php $key = '공공데이터포탈 OpenAPI 인증키'; ?>
    ```
3. 웹서버에서 접속합니다.
4. `현재 키워드` 섹션에 지금 저장된 키워드들이 표시됩니다. `키워드 추가` 또는 `키워드 삭제` 버튼을 눌러 입맛에 맞게 검색 키워드를 설정합니다. 설정된 키워드는 `keywords.xml` 파일에 자동으로 저장됩니다.
5. `검색` 버튼을 눌러 결과를 확인합니다.

---

1. Close this repository and connect it to a web server.
2. Create `servicekey.php` file inside the `assets` folder and put the below code.
    ```php
    // service.php
    <?php $key = 'OpenAPI public key'; ?>
    ```
3. Connect to the solution via a web browser.
4. The currently set keywords are shown. Add or delete the keywords to your needs. The modified keywords are automatically saved in `keywords.xml` file.
5. Search and see results.

## 주의점 (Caution)
* 근본 없는 프로그래밍입니다. 코드가 아주 조악합니다. 답답하실 수도 있습니다... \
As stated above, the code used here is very amateur, and may not be good for your mental health.
* 회사 내부에서 사용하고자 만든 것이기 때문에 UI에 직접링크가 있습니다. 필요에 맞게 수정하시면 됩니다. \
This solution was intended for in-house use, so the UI has in-house direct links hardcoded in the navigation bar. Modify it freely.


## Thanks to...
* [Bootstrap](https://getbootstrap.com/)
* [PHP Simple HTML DOM Parser](https://simplehtmldom.sourceforge.io/)
* [DataTables](https://datatables.net/)
* [공공데이터포탈](https://www.data.go.kr)


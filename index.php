<?php

require 'srcs/simplehtmldom_1_9_1/simple_html_dom.php';

// Form - Date
$startDate = date('Y-m-d', time() - (7 * 24 * 60 * 60));
$endDate = date('Y-m-d');
$startDatePre = DateTime::createFromFormat('Y-m-d', $startDate)->format('Y/m/d');
$endDatePre = DateTime::createFromFormat('Y-m-d', $endDate)->format('Y/m/d');

// XML
$xmlFile = 'keywords.xml';
$xml = new DOMDocument('1.0', "utf-8");
$xml->preserveWhiteSpace = false;
$xml->load($xmlFile);
$xml->formatOutput = true;
$xpath = new DOMXPath($xml);

// 키워드 추가
if (isset($_POST['inputAddKeyword'])) {
    $newKeyword = trim($_POST['inputAddKeyword']);
//    중복검사
    if (count($xpath->query("//keyword[text()=\"$newKeyword\"]")) == 1) {
        echo ("<script>alert('이미 등록된 키워드입니다.');</script>");
    } else {
        $root = $xml->documentElement;
        $newNode = $xml->createElement("keyword", $newKeyword);
        $root->appendChild($newNode);
        $xml->save($xmlFile);
    }
}

// 키워드 삭제
if (isset($_POST['buttonDelete'])) {
    $deleteKeyword = $_POST['buttonDelete'];
    if (count($xpath->query("//keyword[text()=\"$deleteKeyword\"]")) == 1) {
        foreach ($xpath->query("//keyword[text()=\"$deleteKeyword\"]") as $deleteNode) {
            $deleteNode->parentNode->removeChild($deleteNode);
        }
        $xml->save($xmlFile);
    }
}

$keyword = $xml->documentElement->getElementsByTagName('keyword');
$xmlCount = $keyword->length;

// Setting - Currency
$moneyFormat = new NumberFormatter("ko_KR", NumberFormatter::CURRENCY);
$moneyFormat->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 42);
$moneyFormat->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL,",");

// Setting - Date

/// Search logic
if (isset($_POST['buttonSearch'])) {
//    실공고 검색 및 데이터 취합
  $inqryBgnDt = date("Ymd", strtotime($_POST['inputStartDate'])) . '0000';
  $inqryEndDt = date("Ymd", strtotime($_POST['inputEndDate'])) . '0000';

    $html = '';
	$preHtml = '';
	for ($i = 0; $i < $xmlCount; $i++) {
//	    실공고 - 용역
		$ch = curl_init();
		$url = 'http://apis.data.go.kr/1230000/BidPublicInfoService/getBidPblancListInfoServcPPSSrch'; /*URL*/
		$queryParams = '?' . urlencode('ServiceKey') . '=niPQKI7SOhepuEQsEv1Vfh%2B%2BJFh0gdBf%2FRlRVTecK02b0Vo14WBT9%2F5zFw4gIo50GPdcYe37txYZPIGsj1%2Fqbg%3D%3D'; /*Service Key*/
		$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('30'); /* 페이지별 출력수 */
		$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1');
		$queryParams .= '&' . urlencode('inqryDiv') . '=' . urlencode('1');
		$queryParams .= '&' . urlencode('inqryBgnDt') . '=' . urlencode($inqryBgnDt);
		$queryParams .= '&' . urlencode('inqryEndDt') . '=' . urlencode($inqryEndDt);
		$queryParams .= '&' . urlencode('bidNtceNm') . '=' . urlencode($keyword[$i]->nodeValue);
		curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		$responseRealG2B = curl_exec($ch);
		curl_close($ch);

        $xmlRawData = simplexml_load_string($responseRealG2B);
        foreach ($xmlRawData->body->items->item as $item) {
            $html .= '<tr>';
            $html .= "<td>" . date("Y-m-d", strtotime($item->bidNtceDt)) . "</td>";
            $html .= "<td>{$item->ntceKindNm}</td>";
            $html .= "<td>{$item->bidNtceNo}-{$item->bidNtceOrd}</td>";
            $html .= "<td>{$item->bidNtceNm}</td>";
            $html .= "<td>{$item->dminsttNm}</td>";
            $html .= "<td>" . $moneyFormat->format((float)$item->asignBdgtAmt) . "</td>";
            $html .= "<td>" . date("Y-m-d H:m", strtotime($item->bidClseDt)) . "</td>";
            $html .= "<td><a href='{$item->bidNtceDtlUrl}' target='_blank'><i class=\"fas fa-link\"></i></a></td>";
            $html .= '</tr>';
        }
	}

	for ($j = 0; $j < $xmlCount; $j++) {
//	    실공고 - 공사
		$ch = curl_init();
		$url = 'http://apis.data.go.kr/1230000/BidPublicInfoService/getBidPblancListInfoCnstwkPPSSrch'; /*URL*/
		$queryParams = '?' . urlencode('ServiceKey') . '=niPQKI7SOhepuEQsEv1Vfh%2B%2BJFh0gdBf%2FRlRVTecK02b0Vo14WBT9%2F5zFw4gIo50GPdcYe37txYZPIGsj1%2Fqbg%3D%3D'; /*Service Key*/
		$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('30'); /* 페이지별 출력수 */
		$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1');
		$queryParams .= '&' . urlencode('inqryDiv') . '=' . urlencode('1');
		$queryParams .= '&' . urlencode('inqryBgnDt') . '=' . urlencode($inqryBgnDt);
		$queryParams .= '&' . urlencode('inqryEndDt') . '=' . urlencode($inqryEndDt);
		$queryParams .= '&' . urlencode('bidNtceNm') . '=' . urlencode($keyword[$j]->nodeValue);
		curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		$responseRealG2B = curl_exec($ch);
		curl_close($ch);

		$xmlRawData = simplexml_load_string($responseRealG2B);
		foreach ($xmlRawData->body->items->item as $item) {
			$html .= '<tr>';
			$html .= "<td>" . date("Y-m-d", strtotime($item->bidNtceDt)) . "</td>";
			$html .= "<td>{$item->ntceKindNm}</td>";
			$html .= "<td>{$item->bidNtceNo}-{$item->bidNtceOrd}</td>";
			$html .= "<td>{$item->bidNtceNm}</td>";
			$html .= "<td>{$item->dminsttNm}</td>";
			$html .= "<td>" . $moneyFormat->format((float)$item->asignBdgtAmt) . "</td>";
			$html .= "<td>" . date("Y-m-d H:m", strtotime($item->bidClseDt)) . "</td>";
			$html .= "<td><a href='{$item->bidNtceDtlUrl}' target='_blank'><i class=\"fas fa-link\"></i></a></td>";
			$html .= '</tr>';
		}

			// 사전공고
			$preDetailUrl = "https://www.g2b.go.kr:8143/ep/preparation/prestd/preStdDtl.do?preStdRegNo=";
			$preUrl = "http://www.g2b.go.kr:8341/bs/beffatStndrdSearchList.do?cntcSysTyCode=&instCd=&instCl=2&instNm=&prodNm={$keyword[$j]->nodeValue}&rcptDtFrom=$startDatePre&rcptDtTo=$endDatePre&recordCountPerPage=100&searchClCd=search1&swbizTgYn=&taskClCd=0";
			$xmlRawPreData = file_get_html($preUrl);

			$countPre = count($xmlRawPreData->find('table',0)->children(2)->children());
			for ($k = 0; $k < $countPre; $k++) {
//                    echo $xmlRawPreData->find('table',0)->children(2)->children($k);
//                  변수명 설정
				$preCategory = $xmlRawPreData->find('table',0)->children(2)->children($k)->children(0)->plaintext;
				$preNumber = $xmlRawPreData->find('table',0)->children(2)->children($k)->children(1)->plaintext;
				$preName = $xmlRawPreData->find('table',0)->children(2)->children($k)->children(2)->plaintext;
				$preOrg = $xmlRawPreData->find('table',0)->children(2)->children($k)->children(3)->plaintext;
				$preOpenDate = $xmlRawPreData->find('table',0)->children(2)->children($k)->children(4)->plaintext;
				$preEndDate = $xmlRawPreData->find('table',0)->children(2)->children($k)->children(5)->plaintext;
				$preType = $xmlRawPreData->find('table',0)->children(2)->children($k)->children(6)->plaintext;

				$preHtml .= <<<PREHTML
<tr>
    <td>$preCategory</td>
    <td>$preNumber</td>
    <td>$preName</td>
    <td>$preOrg</td>
    <td>$preOpenDate</td>
    <td>$preEndDate</td>
    <td>$preType</td>
    <td><a href="{$preDetailUrl}{$preNumber}" target="_blank"><i class="fas fa-link"></i></a></td>
</tr>
PREHTML;
			}
	}
}

?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/ast.css"/>
    <title>AST 나라장터 공고/사전규격 검색기</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand font-weight-bold" href="./">AST 나라장터 공고/사전규격 검색기</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="https://docs.google.com/spreadsheets/d/1Er-om31yxj8tuFQ6ls2sRXTf3gr_qQ4tmjQIsMotoqI/edit#gid=0" target="_blank">내부검토</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    외부연결
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="//www.g2b.go.kr" target="_blank">나라장터</a>
                    <a class="dropdown-item" href="//rfp.g2b.go.kr" target="_blank">나라장터 RFP</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="//ebid.kwater.or.kr" target="_blank">한국수자원공사 전자발주</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<section class="mt-4 container">
    <form method="post">
        <div class="form-group row mb-0">
            <label class="col-sm-2 col-form-label">날짜범위</label>
            <div class="form-group col-sm-5">
                <input type="date" class="form-control" name="inputStartDate" value="<?= $startDate; ?>"/>
                <small class="form-text text-muted">시작일</small>
            </div>
            <div class="form-group col-sm-5">
                <input type="date" class="form-control" name="inputEndDate" value="<?= $endDate; ?>"/>
                <small class="form-text text-muted">종료일</small>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <p class="font-weight-bold">현재 키워드</p>
            </div>
            <div class="col-sm-10">
                <p>
                  <?php
                    for ($i = 0; $i < $xmlCount; $i++) {
                        echo "<span class=\"badge badge-pill badge-info\">".trim($keyword[$i]->textContent)."</span>";
                    }
                  ?>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 offset-sm-2">
                <button type="button" class="btn btn-outline-info btn-block btn-sm" data-toggle="modal" data-target="#addModal">키워드 추가</button>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-outline-danger btn-block btn-sm" data-toggle="modal" data-target="#keywordModal">키워드 삭제</button>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-4 offset-sm-2">
                <button type="submit" class="btn btn-primary btn-block" name="buttonSearch" value="run">검색</button>
            </div>
        </div>
    </form>
</section>

<?php if(isset($_POST['buttonSearch'])) {
	echo <<<TABLE
<hr class="container border-secondary" />
<!-- 결과 테이블 -->
<section class="container-fluid mt-2">
<div class="container">
    <div class="alert alert-primary">
        <h5 class="mb-0">실제공고</h5>
    </div>
</div>
    <div class="row justify-content-center">
        <div class="col-auto">
            <table class="table table-sm table-hover table-striped table-responsive"  id="resultTable">
                <thead class="text-center">
                    <tr>
                        <th>공고일</th>
                        <th>분류</th>
                        <th>공고번호</th>
                        <th>공고</th>
                        <th>수요기관</th>
                        <th>배정예산</th>
                        <th>마감일</th>
                        <th>링크</th>
                    </tr>
                </thead>
                <tbody>
                $html
                </tbody>
            </table>
        </div>
    </div>
<div class="container mt-4">
    <div class="alert alert-info">
        <h5 class="mb-0">사전규격</h5>
    </div>
</div>
    <div class="row justify-content-center mt-4">
        <div class="col-auto">
            <table class="table table-sm table-hover table-striped table-responsive" id="resultTablePre">
                <thead class="text-center">
                <tr>
                    <th>분류</th>
                    <th>사전규격번호</th>
                    <th>공고명</th>
                    <th>수요기관</th>
                    <th>공개일</th>
                    <th>마감일</th>
                    <th>형태</th>
                    <th>링크</th>
                </tr>
                </thead>
                <tbody>
                $preHtml
                </tbody>
            </table>
        </div>
    </div>
</section>
TABLE;
}
?>

<!-- 키워드 삭제 모달-->
<div class="modal fade" id="keywordModal" data-backdrop="static" tabindex="-2" role="dialog" aria-labelledby="keywordModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keywordModalTitle">키워드 목록</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <p class="form-text text-danger float-right"><strong>삭제</strong> 버튼을 누르면 즉시 삭제됩니다!</p>
                  <?php
                  for ($i = 0; $i < $xmlCount; $i++) {
                      $nodeValue = $keyword[$i]->nodeValue;
                      echo <<<HTML
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="{$nodeValue}" readonly/>
                            <div class="input-group-append">
                                <button class="btn btn-outline-danger" type="submit" name="buttonDelete" value="{$nodeValue}">삭제</button>
                            </div>
                        </div>
HTML;
                  }
                  ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">닫기</button>
            </div>
        </div>
    </div>
</div>

<!--키워드 추가 모달 -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalBody" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-body">
                    <p id="addModalBody">추가할 키워드를 입력해 주세요.</p>
                    <input type="text" class="form-control is-valid" name="inputAddKeyword" />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-danger">추가</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">닫기</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#resultTable').DataTable({
        "order": [[ 0, "desc" ]]
    });

    $('#resultTablePre').DataTable({
        "order": [[ 4, "desc" ]]
    });
});
</script>
</body>
</html>

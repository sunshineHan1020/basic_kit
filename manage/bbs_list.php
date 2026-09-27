<?include $_SERVER['DOCUMENT_ROOT'].'/admin/inc/header.php';?>


<main class="content">

<div class="page-header">
<div class="page-title">
<span>BLOG</span><i class="ri-arrow-right-s-line"></i><h2>게시글 관리</h2>
</div>
<div class="page-action">
<button class="btn btn-white">선택삭제</button>
<button class="btn btn-white">엑셀 다운로드</button>
<button class="btn btn-primary">게시글 등록</button>
</div>
</div>

<div class="card search-card">
<div class="card-header"><h3>검색조건</h3></div>
<div class="card-body">

<div class="search-grid">
<div class="form-group"><label>제목</label><input type="text" placeholder="검색어"></div>
<div class="form-group"><label>카테고리</label><select><option>전체</option></select></div>
<div class="form-group"><label>공개여부</label><select><option>전체</option><option>공개</option><option>비공개</option></select></div>
<div class="form-group"><label>작성자</label><input type="text"></div>
<div class="form-group"><label>기간</label><input type="date"></div>
<div class="form-group"><label>~</label><input type="date"></div>
<div class="form-group"><label>태그</label><input type="text"></div>
<div class="form-group"><label>조회수</label><select><option>전체</option></select></div>
</div>

<div class="search-action">
<button class="btn btn-white">초기화</button>
<button class="btn btn-primary">검색</button>
</div>

</div>
</div>

<div class="card">
<div class="card-header">
<h3>게시글 목록</h3>
</div>

<div class="card-body">

<div class="list-toolbar">

    <div class="list-count">
        <strong>총 <em>230</em>건</strong>
    </div>

    <div class="list-option">
        <select>
            <option>20개씩 보기</option>
            <option>30개씩 보기</option>
            <option>50개씩 보기</option>
            <option>100개씩 보기</option>
        </select>
    </div>

</div>

<table class="table table-list">
<thead>
<tr>
<th width="40"><input type="checkbox"></th>
<th width="70">번호</th>
<th width="90">썸네일</th>
<th>제목</th>
<th width="100">카테고리</th>
<th width="90">공개</th>
<th width="80">조회</th>
<th width="100">작성자</th>
<th width="120">작성일</th>
<th width="170">관리</th>
</tr>
</thead>
<tbody>

<tr>
<td><input type="checkbox"></td>
<td>230</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 1</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>103</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>229</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 2</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>106</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>228</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 3</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>109</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>227</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 4</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>112</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>226</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 5</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>115</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>225</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 6</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>118</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>224</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 7</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>121</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>223</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 8</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>124</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>222</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 9</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>127</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>221</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 10</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>130</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>220</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 11</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>133</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>219</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 12</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>136</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>218</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 13</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>139</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>217</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 14</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>142</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>216</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 15</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>145</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>215</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 16</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>148</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>214</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 17</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>151</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>213</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 18</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>154</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>212</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 19</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>157</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>211</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 20</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>160</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>210</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 21</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>163</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>209</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 22</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>166</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>208</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 23</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>169</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>207</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 24</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>172</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>206</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 25</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>175</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>205</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 26</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>178</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>204</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 27</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>181</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>203</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 28</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>184</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>202</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 29</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-public">공개</span></td>
<td>187</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

<tr>
<td><input type="checkbox"></td>
<td>201</td>
<td><div class="table-thumb"><img src="https://placehold.co/120x80" alt=""></div></td>
<td>
<div class="post-title">
<strong>Weekends 리뉴얼 프로젝트 게시글 30</strong>
<p>간단한 요약 설명이 노출됩니다.</p>
</div>
</td>
<td>News</td>
<td><span class="status-private">비공개</span></td>
<td>190</td>
<td>관리자</td>
<td>2026-07-23</td>
<td class="table-action">
<button class="btn btn-white btn-sm">수정</button>
<button class="btn btn-danger btn-sm">삭제</button>
</td>
</tr>

</tbody>
</table>

<div class="empty-state" style="display:none">
<p>등록된 게시글이 없습니다.</p>
</div>

<div class="pagination">
<button>&laquo;</button>
<button class="active">1</button>
<button>2</button>
<button>3</button>
<button>4</button>
<button>5</button>
<button>&raquo;</button>
</div>

</div>
</div>

</main>

</body>
</html>

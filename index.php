<!DOCTYPE html>
<html lang="ja">


<head>
  
    <meta charset="UTF-8">
    <!--　画面表示　-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--　ブラウザのツールバーに表示 -->
    <title>music life</title>

    <!-- swiperを読み込むための設定-->
    <link rel="stylesheet" href="./swiper/swiper.min.css">
    <!-- FontAwsomeを読み込むための設定-->
    <link rel="stylesheet" href="./fontawesome/css/all.css">

    <!-- CSSを外部から読み込むときに以下のコードが必要 -->
    <link rel="stylesheet" type="text/css" href="./style.css">

    <!-- swiperを読み込むための設定② -->
    <script src="./swiper/swiper.min.js"></script>
    
     <!--　Googleフォント設定-->
    <!--デザイン-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Chela+One&family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <!--文章-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Chela+One&display=swap" rel="stylesheet">
    
        <!--　Googleフォント設定-->
    <!--デザイン-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Chela+One&family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <!--文章-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Chela+One&display=swap" rel="stylesheet">
    
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-218910739-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-218910739-2');
</script>
    </script>
</head>

<body>
    <header>
        <div class="menu-container">
            <div class="menu-wrapper">
                <div class="logo">Music Life</div>
            </div>
        </div>
        <div class="swiper-container">

            <div class="swiper-wrapper">

                <div class="swiper-slide slide01">
                </div>
                <div class="swiper-slide slide02">
                </div>
            </div>

            <div class="swiper-button">
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>

            <div class="swiper-pagination"></div>

        </div>
    </header>

    <main>
        
        <h1>YouTube Music Search</h1>

        <!-- 曲の検索バー -->
        <div class="search-bar">
            <input type="text" id="keyword" autocomplete="off" placeholder="洋楽 おすすめ 2021">
            <button onclick="search()">検索</button>
        </div>


        <!-- 検索結果が表示される -->
        <div class="search-result" id="APIResult"></div>
        
    </main>

    <footer>
        <small>&copy; 2021 Music Life</small>
    </footer>

    <!-- ページ上部に移動するボタン -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.4/css/all.css">
    <div id="page_top"><a href="#"></a></div>



    <!-- JavaScript -->
    <script type="text/javascript">
        var swiper = new Swiper('.swiper-container', {

            // スライド操作
            speed: 1000, // スピード設定 1000=1秒
            autoplay: true, // 自動切り替え trueで有効 falseで無
            loop: true, // ループ trueで有効 falseで無効
            navigation: {
                nextEl: '.swiper-button-next', // 次のボタンを表示する要素指定
                prevEl: '.swiper-button-prev' // 前のボタンを表示する要素指定
            },
            pagination: {
                el: '.swiper-pagination', // ページネーションを表示する要素指定
            }

        });

      // --- 検索履歴追加 ---
function addHistory(img, keyword) {
    // (1) 履歴に検索キーワードが存在するか
    var exists = $.grep($("#history li"), function(item, index){
        return ($(item).children(".key").text() == keyword);
    });

    if (exists.length == 0) {    // (2) 存在しない
        $("<li/>")
            .append(img).append("<br/>")
            .append($("<span/>").addClass("key").append(keyword))
            .append(
                $("<a/>").addClass("del").append("[x]")
                .click(function(){
                    $(this).parent().remove();
                    if (searchCond.keyword == keyword) {
                        $("#videos").empty();
                        $("#result").empty();
                    }
                })
            )
            .click(function(){searchHistory({"keyword":keyword, "page":1, "orderby":"relevance"});})
            .prependTo("#history > ul");
    } else {    // (3) 存在する
        $(exists)
            .prependTo($(exists).parent())
            .children("img").attr("src", img.attr("src"));

    }

}
        // 発行したAPIKey
        const APIkey = "AIzaSyDSRdpdrBTU4wYh5oLQr5s4G4F3hnGS0-U";
        // 検索結果が表示されるHTMLの要素を取得
        const APIResult = document.getElementById("APIResult");


        // 検索ボタンが押された時(非同期処理: async)
        async function search() {

            // 入力したキーワードの取得
            const keywords = document.getElementById("keyword").value;

            // 検索結果を画面表示する時のHTMLが入る
            let insertHTML = "";

            if (keywords == "") { // 検索キーワードが空欄の時

                insertHTML = "<p style='color:red'>キーワードを入力してください。</p>";

            } else { // 検索キーワードがある時

                // YouTube APIにアクセス(検索キーワードと発行したAPIKeyも含む)
                const data = await fetch("https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=10&q=" + keywords + "&key=" + APIkey);
                // YouTube APIの返ってきたテキストを使いやすいように、JSONというものに変換する
                const json = await data.json();
                // YouTubeの検索結果の件数(MAX: 5件)
                const data_length = json.items.length;

                if (data_length > 0) { // 検索結果の件数が、0件より大きい時

                    // 検索結果の件数分繰り返す
                    for (let i = 0; i < data_length; i++) {

                        // YouTube動画の1つ1つの動画のID
                        const videoid = json.items[i].id.videoId;

                        // YouTubeの1つ1つの動画のIDをもとに、YouTubeをHTMLに埋め込む準備
                        insertHTML += "<div class='items'><iframe src='https://www.youtube.com/embed/" + videoid + "'></iframe></div>";

                    }

                } else { // 該当する検索結果がなかった時

                    insertHTML = "該当する動画が見つかりませんでした。";

                }
            }

            // 検索結果を「<div class="result" id="APIResult"></div>」の中に書き込んで表示する
            APIResult.innerHTML = insertHTML;
        }

    </script>
</body>

</html>

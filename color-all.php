<?php
//列出所有的颜色
require_once('functions/settings.php');
require_once(ABSPATH . '/wp-load.php');

get_header();
?>

<div id="luka">
  <div class="hamburger"></div>
  <p><a href="/">色彩 - <?php echo get_bloginfo(); ?></a></p>
</div>

<div id="page">
    <div id="overview">
        <div class="headerSpacer"></div>

          <?php if(!is_user_logged_in()): ?>
      <!--  欢迎语   -->
      <div id="siteIntro">
          <h2>欢迎来到小摄郎，在这里发现并分享美丽的影与像。</h2>
          <a href="/signup" class="actionButton blueButton">现在加入</a>
      </div>
      <?php endif; ?>

      <div id="colorsTile" class="tileTrash">
        <ol class="clearfix">
          <li data-hex="EE99AA" data-r="244" data-g="147" data-b="170" style="background-color: #F493AA;"><span></span></li>
          <li data-hex="EEAACC" data-r="231" data-g="165" data-b="198" style="background-color: #E7A5C6;"><span></span></li>
          <li data-hex="CCAACC" data-r="208" data-g="175" data-b="201" style="background-color: #D0AFC9;"><span></span></li>
          <li data-hex="BBAACC" data-r="190" data-g="176" data-b="204" style="background-color: #BEB0CC;"><span></span></li>
          <li data-hex="AAAACC" data-r="170" data-g="176" data-b="205" style="background-color: #AAB0CD;"><span></span></li>
          <li data-hex="AAAACC" data-r="163" data-g="178" data-b="206" style="background-color: #A3B2CE;"><span></span></li>
          <li data-hex="AABBDD" data-r="167" data-g="192" data-b="220" style="background-color: #A7C0DC;"><span></span></li>
          <li data-hex="BBDDEE" data-r="181" data-g="222" data-b="234" style="background-color: #B5DEEA;"><span></span></li>
          <li data-hex="BBDDCC" data-r="185" data-g="221" data-b="207" style="background-color: #B9DDCF;"><span></span></li>
          <li data-hex="BBDDAA" data-r="184" data-g="216" data-b="172" style="background-color: #B8D8AC;"><span></span></li>
          <li data-hex="BBDDAA" data-r="191" data-g="218" data-b="167" style="background-color: #BFDAA7;"><span></span></li>
          <li data-hex="DDDD99" data-r="215" data-g="226" data-b="160" style="background-color: #D7E2A0;"><span></span></li>
          <li data-hex="FFEE88" data-r="249" data-g="234" data-b="142" style="background-color: #F9EA8E;"><span></span></li>
          <li data-hex="FFCC99" data-r="249" data-g="196" data-b="148" style="background-color: #F9C494;"><span></span></li>
          <li data-hex="EE9999" data-r="244" data-g="160" data-b="151" style="background-color: #F4A097;"><span></span></li>
          <li data-hex="EE9999" data-r="239" data-g="153" data-b="158" style="background-color: #EF999E;"><span></span></li>
          <li data-hex="EE5577" data-r="237" data-g="93" data-b="126" style="background-color: #ED5D7E;"><span></span></li>
          <li data-hex="DD77AA" data-r="218" data-g="119" data-b="169" style="background-color: #DA77A9;"><span></span></li>
          <li data-hex="BB88AA" data-r="184" data-g="135" data-b="174" style="background-color: #B887AE;"><span></span></li>
          <li data-hex="9988AA" data-r="156" data-g="135" data-b="178" style="background-color: #9C87B2;"><span></span></li>
          <li data-hex="7788BB" data-r="126" data-g="136" data-b="180" style="background-color: #7E88B4;"><span></span></li>
          <li data-hex="7788BB" data-r="117" data-g="138" data-b="181" style="background-color: #758AB5;"><span></span></li>
          <li data-hex="7799CC" data-r="123" data-g="160" data-b="202" style="background-color: #7BA0CA;"><span></span></li>
          <li data-hex="88CCDD" data-r="144" data-g="204" data-b="222" style="background-color: #90CCDE;"><span></span></li>
          <li data-hex="99CCBB" data-r="149" data-g="203" data-b="183" style="background-color: #95CBB7;"><span></span></li>
          <li data-hex="99CC88" data-r="147" data-g="196" data-b="130" style="background-color: #93C482;"><span></span></li>
          <li data-hex="99CC77" data-r="159" data-g="199" data-b="122" style="background-color: #9FC77A;"><span></span></li>
          <li data-hex="BBCC77" data-r="194" data-g="211" data-b="111" style="background-color: #C2D36F;"><span></span></li>
          <li data-hex="EEDD55" data-r="245" data-g="223" data-b="85" style="background-color: #F5DF55;"><span></span></li>
          <li data-hex="EEAA66" data-r="245" data-g="166" data-b="94" style="background-color: #F5A65E;"><span></span></li>
          <li data-hex="EE7766" data-r="237" data-g="112" data-b="98" style="background-color: #ED7062;"><span></span></li>
          <li data-hex="EE6666" data-r="231" data-g="102" data-b="109" style="background-color: #E7666D;"><span></span></li>
          <li data-hex="EE2255" data-r="231" data-g="38" data-b="83" style="background-color: #E72653;"><span></span></li>
          <li data-hex="CC4488" data-r="205" data-g="73" data-b="140" style="background-color: #CD498C;"><span></span></li>
          <li data-hex="996699" data-r="160" data-g="94" data-b="146" style="background-color: #A05E92;"><span></span></li>
          <li data-hex="776699" data-r="123" data-g="95" data-b="152" style="background-color: #7B5F98;"><span></span></li>
          <li data-hex="556699" data-r="83" data-g="96" data-b="154" style="background-color: #53609A;"><span></span></li>
          <li data-hex="446699" data-r="70" data-g="99" data-b="156" style="background-color: #46639C;"><span></span></li>
          <li data-hex="5588BB" data-r="78" data-g="128" data-b="184" style="background-color: #4E80B8;"><span></span></li>
          <li data-hex="66BBCC" data-r="106" data-g="187" data-b="211" style="background-color: #6ABBD3;"><span></span></li>
          <li data-hex="77BB99" data-r="113" data-g="185" data-b="158" style="background-color: #71B99E;"><span></span></li>
          <li data-hex="77AA55" data-r="111" data-g="176" data-b="88" style="background-color: #6FB058;"><span></span></li>
          <li data-hex="77BB55" data-r="126" data-g="180" data-b="77" style="background-color: #7EB44D;"><span></span></li>
          <li data-hex="AACC44" data-r="173" data-g="196" data-b="63" style="background-color: #ADC43F;"><span></span></li>
          <li data-hex="EECC22" data-r="241" data-g="212" data-b="28" style="background-color: #F1D41C;"><span></span></li>
          <li data-hex="EE8822" data-r="241" data-g="136" data-b="40" style="background-color: #F18828;"><span></span></li>
          <li data-hex="EE4433" data-r="231" data-g="64" data-b="45" style="background-color: #E7402D;"><span></span></li>
          <li data-hex="DD3344" data-r="222" data-g="50" data-b="60" style="background-color: #DE323C;"><span></span></li>
          <li data-hex="AA2244" data-r="173" data-g="29" data-b="62" style="background-color: #AD1D3E;"><span></span></li>
          <li data-hex="993366" data-r="154" data-g="55" data-b="105" style="background-color: #9A3769;"><span></span></li>
          <li data-hex="774466" data-r="120" data-g="71" data-b="110" style="background-color: #78476E;"><span></span></li>
          <li data-hex="554477" data-r="92" data-g="71" data-b="114" style="background-color: #5C4772;"><span></span></li>
          <li data-hex="444477" data-r="62" data-g="72" data-b="116" style="background-color: #3E4874;"><span></span></li>
          <li data-hex="334477" data-r="53" data-g="74" data-b="117" style="background-color: #354A75;"><span></span></li>
          <li data-hex="336688" data-r="59" data-g="96" data-b="138" style="background-color: #3B608A;"><span></span></li>
          <li data-hex="558899" data-r="80" data-g="140" data-b="158" style="background-color: #508C9E;"><span></span></li>
          <li data-hex="558877" data-r="85" data-g="139" data-b="119" style="background-color: #558B77;"><span></span></li>
          <li data-hex="558844" data-r="83" data-g="132" data-b="66" style="background-color: #538442;"><span></span></li>
          <li data-hex="668833" data-r="95" data-g="135" data-b="58" style="background-color: #5F873A;"><span></span></li>
          <li data-hex="889933" data-r="130" data-g="147" data-b="47" style="background-color: #82932F;"><span></span></li>
          <li data-hex="BB9911" data-r="181" data-g="159" data-b="21" style="background-color: #B59F15;"><span></span></li>
          <li data-hex="BB6622" data-r="181" data-g="102" data-b="30" style="background-color: #B5661E;"><span></span></li>
          <li data-hex="AA3322" data-r="173" data-g="48" data-b="34" style="background-color: #AD3022;"><span></span></li>
          <li data-hex="AA2233" data-r="167" data-g="38" data-b="45" style="background-color: #A7262D;"><span></span></li>
          <li data-hex="771122" data-r="116" data-g="19" data-b="42" style="background-color: #74132A;"><span></span></li>
          <li data-hex="662244" data-r="103" data-g="37" data-b="70" style="background-color: #672546;"><span></span></li>
          <li data-hex="553344" data-r="80" data-g="47" data-b="73" style="background-color: #502F49;"><span></span></li>
          <li data-hex="443344" data-r="62" data-g="48" data-b="76" style="background-color: #3E304C;"><span></span></li>
          <li data-hex="223355" data-r="42" data-g="48" data-b="77" style="background-color: #2A304D;"><span></span></li>
          <li data-hex="223355" data-r="35" data-g="50" data-b="78" style="background-color: #23324E;"><span></span></li>
          <li data-hex="224455" data-r="39" data-g="64" data-b="92" style="background-color: #27405C;"><span></span></li>
          <li data-hex="336666" data-r="53" data-g="94" data-b="106" style="background-color: #355E6A;"><span></span></li>
          <li data-hex="335555" data-r="57" data-g="93" data-b="79" style="background-color: #395D4F;"><span></span></li>
          <li data-hex="335533" data-r="56" data-g="88" data-b="44" style="background-color: #38582C;"><span></span></li>
          <li data-hex="445522" data-r="63" data-g="90" data-b="39" style="background-color: #3F5A27;"><span></span></li>
          <li data-hex="556622" data-r="87" data-g="98" data-b="32" style="background-color: #576220;"><span></span></li>
          <li data-hex="776611" data-r="121" data-g="106" data-b="14" style="background-color: #796A0E;"><span></span></li>
          <li data-hex="774411" data-r="121" data-g="68" data-b="20" style="background-color: #794414;"><span></span></li>
          <li data-hex="772211" data-r="116" data-g="32" data-b="23" style="background-color: #742017;"><span></span></li>
          <li data-hex="771122" data-r="111" data-g="25" data-b="30" style="background-color: #6F191E;"><span></span></li>
          <li data-hex="331111" data-r="58" data-g="10" data-b="21" style="background-color: #3A0A15;"><span></span></li>
          <li data-hex="331122" data-r="51" data-g="18" data-b="35" style="background-color: #331223;"><span></span></li>
          <li data-hex="221122" data-r="40" data-g="24" data-b="37" style="background-color: #281825;"><span></span></li>
          <li data-hex="221122" data-r="31" data-g="24" data-b="38" style="background-color: #1F1826;"><span></span></li>
          <li data-hex="111122" data-r="21" data-g="24" data-b="39" style="background-color: #151827;"><span></span></li>
          <li data-hex="111122" data-r="18" data-g="25" data-b="39" style="background-color: #121927;"><span></span></li>
          <li data-hex="112233" data-r="20" data-g="32" data-b="46" style="background-color: #14202E;"><span></span></li>
          <li data-hex="223333" data-r="27" data-g="47" data-b="53" style="background-color: #1B2F35;"><span></span></li>
          <li data-hex="223322" data-r="28" data-g="46" data-b="40" style="background-color: #1C2E28;"><span></span></li>
          <li data-hex="223311" data-r="28" data-g="44" data-b="22" style="background-color: #1C2C16;"><span></span></li>
          <li data-hex="223311" data-r="32" data-g="45" data-b="19" style="background-color: #202D13;"><span></span></li>
          <li data-hex="333311" data-r="43" data-g="49" data-b="16" style="background-color: #2B3110;"><span></span></li>
          <li data-hex="443300" data-r="60" data-g="53" data-b="7" style="background-color: #3C3507;"><span></span></li>
          <li data-hex="442211" data-r="60" data-g="34" data-b="10" style="background-color: #3C220A;"><span></span></li>
          <li data-hex="331111" data-r="58" data-g="16" data-b="11" style="background-color: #3A100B;"><span></span></li>
          <li data-hex="331111" data-r="56" data-g="13" data-b="15" style="background-color: #380D0F;"><span></span></li>
          <li data-hex="000000" data-r="0" data-g="0" data-b="0" style="background-color: #000000;"><span></span></li>
          <li data-hex="111111" data-r="16" data-g="16" data-b="16" style="background-color: #101010;"><span></span></li>
          <li data-hex="222222" data-r="32" data-g="32" data-b="32" style="background-color: #202020;"><span></span></li>
          <li data-hex="333333" data-r="48" data-g="48" data-b="48" style="background-color: #303030;"><span></span></li>
          <li data-hex="444444" data-r="64" data-g="64" data-b="64" style="background-color: #404040;"><span></span></li>
          <li data-hex="555555" data-r="80" data-g="80" data-b="80" style="background-color: #505050;"><span></span></li>
          <li data-hex="666666" data-r="96" data-g="96" data-b="96" style="background-color: #606060;"><span></span></li>
          <li data-hex="777777" data-r="112" data-g="112" data-b="112" style="background-color: #707070;"><span></span></li>
          <li data-hex="888888" data-r="128" data-g="128" data-b="128" style="background-color: #808080;"><span></span></li>
          <li data-hex="888888" data-r="144" data-g="144" data-b="144" style="background-color: #909090;"><span></span></li>
          <li data-hex="999999" data-r="160" data-g="160" data-b="160" style="background-color: #A0A0A0;"><span></span></li>
          <li data-hex="AAAAAA" data-r="176" data-g="176" data-b="176" style="background-color: #B0B0B0;"><span></span></li>
          <li data-hex="BBBBBB" data-r="192" data-g="192" data-b="192" style="background-color: #C0C0C0;"><span></span></li>
          <li data-hex="CCCCCC" data-r="208" data-g="208" data-b="208" style="background-color: #D0D0D0;"><span></span></li>
          <li data-hex="DDDDDD" data-r="224" data-g="224" data-b="224" style="background-color: #E0E0E0;"><span></span></li>
          <li data-hex="EEEEEE" data-r="240" data-g="240" data-b="240" style="background-color: #F0F0F0;"><span></span></li>
        </ol>
      </div>

      <div id="images">
        <div id="clue" class="tileTrash">
          <h2>点击上面的色块查看详细颜色的图片</h2>
        </div>
        <div class="clear"></div>
      </div>

      <script type="text/javascript"> var pageConfig = {type:'color'}; </script>
    </div><!-- overview 结束 -->
</div>
<?php get_footer(); ?>
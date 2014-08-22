<?php
//echo exec("/usr/local/bin/convert /var/tmp/5107e03620070.png temp/5107e03620070.jpg", $out); 
//var_dump($out);
$s = 'cmd=_xclick&business=AFQF9M9HBTJUC&lc=DE&item_name=Dein+St%FCck+Afrika&item_number=__donationid__&amount=__amount__&currency_code=EUR&button_subtype=services&no_note=1&no_shipping=1&rm=1&return=__success__&cancel_return=__cancel__&bn=PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted&notify_url=__ipn__&page_style=DSA&cbt=Dein+Stück+Afrika';
parse_str($s, $result);

var_dump($result);

echo var_export($result);

//var_dump(passthru("/usr/local/bin/convert /var/tmp/5107e03620070.pdf -density 72 -resize 50% /var/tmp/5107e03620070.png"));

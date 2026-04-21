<?php
/**
 * ----------------------------------------------------------------
 * Maintenance mode for visitors not logged in
 * ----------------------------------------------------------------
 */
/*
function l4k_maintenance() {

    if (!is_user_logged_in()) {

        if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false ||
            strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false ||
            strpos($_SERVER['REQUEST_URI'], 'admin-ajax.php') !== false
        ) {
            return;
        }

        // Show maintenance page
        wp_die(
            '<h1>Coming Soon!</h1><p>We’re cooking something new, please check again in a couple of weeks.</p>',
            'Coming Soon',
            array('response' => 503)
        );
    }

}

add_action( 'init', 'l4k_maintenance' );
*/

/**
 * ----------------------------------------------------------------
 * Quick insert of new titles (to be deleted when not needed)
 * ----------------------------------------------------------------
 */
/*
add_action('init', function () {

    if (!isset($_GET['seed_videos'])) return; // run only with URL trigger

    // use http://lote4kids.local/?seed_videos=1

    $titles = explode(',', "A Fox Alone,A Shipwreck Mystery,Charlie and Chad,Chick\'s Trick,Chicken Licken,Cupcake,Green, Green Grapes,Hide and Cheep,My Colours,My Garden,Polly Panda and the Red Thing,The Dreadful Drip,The Golden Goose,The Princess and the Pea,The Princess Twins,The Trike to Trenton,Then the Wind Came,Theo\'s Wobbly Tooth,What a Lot of Fun,Zack\'s Socks");
    $descriptions = explode(',', '<i>Eric Fein</i><br>Nakahanap ang isang mangingisda ng isang lumang bote at, mula sa loob ng bote, lumitaw ang isang masamang genie at nagbabantang kakainin ang mangingisda. Makatakas kaya ang mangingisda?,<i>Robert Southey</i><br>,<i>Upton Sinclair</i><br>,<i>Charles Perrault</i><br>,<i>Aesop</i><br>Isang aso ang may dalang kapirasong karne pauwi nang makita niya ang sarili niyang repleksyon sa isang batis. Sa pag-iisip na ito ay isa pang aso na may isa pang piraso ng karne, matakaw siyang tumalon, nawala ang pagkain.,<i>Aesop</i><br>Ipinagyayabang ng Hare kung gaano siya kabilis tumakbo ngunit pumayag ang Pagong na makipagkarera laban sa kanya. Sino ang mananalo sa karerang ito?,<i>Aesop</i><br>Isang daga ang gumising sa isang leon at matapos mahuli ay nangako na kung ang leon ay magligtas sa kanyang buhay ay magiging kaibigan niya ito magpakailanman. Paano magiging maayos ang pagkakaibigang ito?,<i>Aesop</i><br>Ang Araw at ang Hangin ay nakipagpustahan upang makita kung sino ang maaaring magtanggal ng amerikana ng isang lalaki. Sino ang mananalo sa taya na ito?,<i>Peter Christen Asbjørnsen and Jørgen Moe</i><br>,<i>Aesop</i><br>,<i>Amanda Graham & Naomi Lewis</i><br>Gusto ni Aryo na diwata ang kanyang mapangasawa. Kaya ninakaw niya ang alampay ng isang diwata, para hindi ito makalipad. Pero may mahika pa rin ang diwata. Ano ang gagawin niya?,<i>Richard Liu & Si-Cong Ding</i><br>Si Cao Cao, ang pinuno ng kaharian, ay nakatanggap ng isang elepante bilang regalo. Dapat malaman ng kanyang mga ministro kung gaano ito kabigat. Paano nila titimbangin ang elepante?,<i>Josephine Croser  & Donna Gynell</i><br>Hindi gusto ng dalawang palaka ang kanilang mga tahanan. Kaya iniisip nila kung dapat ba silang lumipat ng tahanan. Pero mas magugustuhan kaya nila ang tahanan ng isa\'t isa?,<i>Richard Liu & Gabriel Cunnett</i><br>Si Unggoy ay nakarinig ng nakakatakot, malakas na tunog. Kaya sinabi niya ito kina Ardilya, Kuneho at Usa. Natakot din ang mga ito. Ngunit hindi natakot si Tigre. Ano kaya ang tunog na iyon?,<i>Richard Liu & Kai-Lun Wang</i><br>Si Oriole ay masyadong mahiyain para kumanta sa konsiyerto sa kagubatan. Kaya humingi siya ng tulong sa iba pang mga ibon. Magkakaroon ba siya ng sapat na tapang upang kumanta?,<i>Jill McDougall  & Carol McLean-Carr</i><br>Ang Emperador ay nagdaraos ng paligsahan para sa kanyang mga anak. Ang bawat prinsipe ay dapat magluto ng isang espesyal na putahe, at ang mananalo ay ang magiging bagong Emperador.,<i>Richard Lui & Connie Mavromatis</i><br>Ang mga manggagawang langgam ay nagagalit sa isa pang langgam. Sa tingin nila ay hindi siya tumutulong nang sapat. Pero ano nga ba talaga ang ginagawa ng isa pang langgam?,<i>Rodney Martin & Leanne Argent</i><br>Nais ng nakatataas na daga sa bayan ng isang malakas na asawa para sa kanyang anak na babae. Sino o ano ang magiging pinakamalakas na asawa?,<i>Richard Liu & Marsha Wajer</i><br>Ang buwan ay nahulog sa balon! Ang sabi ng batang unggoy sa mga nakatatandang unggoy. Kaya gumawa ng plano ang mga unggoy. Ano ang gagawin nila?,<i>Josephine Croser & Pat Reynolds</i><br>Inaakala ng isang unggoy na siya ay nagmamay-ari ng puno ng mangga. Hindi niya hinayaang kainin ng asong lobo, ibon o oso ang mangga. Pero naloko ng ibang hayop ang unggoy.,<i>Nigel Croser & Naomi Lewis</i><br>Isang lalaki ang nangongolekta ng niyog para iuwi sa kanyang asawa. Sabi ng isang batang lalaki, "Kung nagmamadali ka, gagabihin ka pag-uwi." Ano ang gagawin niya?,<i>Josephine Croser & Marsha Wajer</i><br>Ang isang  asong racoon ay nailigtas mula sa isang patibong ng isang mabait na lalaki. Kaya ginamit ng asong raccoon ang kanyang espesyal na mahika upang bayaran ang kabaitang iyon. Ano ang ginawa niya?,<i>Jill McDougall & Laura Peterson</i><br>Isang matandang lalaki at babae ang nakakita ng isang batang lalaki sa loob ng isang peach. Momotaro ang tawag nila sa kanya. Nang maglaon, isang grupo ng masasamang dambuhala ang pumasok sa nayon at nangambala sa mga tao. Mapaalis kaya ni Momotaro ang mga dambuhala?,<i>Yvonne Winer & Susy Boyer</i><br>Matalik na magkaibigan ang Prinsesang Dragon at si Dikya. Ngunit si Pugita ay nagseselos at gumawa ng isang malupit at masamang panlilinlang kay Dikya. Ano ang mangyayari sa kanila?,<i>Josephine Croser & Leanne Argent</i><br>Isang magsasaka at ang kanyang asawa ay napakahirap. Isang araw, isang misteryosong dalaga ang lumitaw sa kanilang pintuan. Naghahabi siya ng magandang tela para sa kanila. Sino ang babaeng ito?,<i>Michael Steer & Nathan Kolic</i><br>Si Prinsipe Rama ay pinalayas mula sa kaharian kasama sina Sita, ang kanyang asawa, at si Lakshmana na kanyang kapatid. Ninakaw ng masamang hari na si  Haring Ravana ang kanyang asawa kaya tinawag nila si Hanuman, ang Haring Unggoy para tumulong.,<i>Richard Liu & Vy Vu</i><br>Si Si Ma Guang ay naglalaro ng taguan kasama ang kanyang mga kaibigan. Ngunit ang isa sa mga batang lalaki ay nahulog sa isang banga ng tubig! Maililigtas kaya siya ni Si Ma Guang?,<i>Jill McDougall  & Annie McQueen</i><br>Gustong tumawid ni Pilandok sa ilog. Ngunit may mga buwaya roon! Kaya nag-isip siya ng plano. Paano niya malalampasan ang mga buwaya?,<i>Josephine Croser  & Connie Mavromatis</i><br>Si Pilandok ay naipit sa isang butas! Pagkatapos ay narinig niya na paparating si Elepante. Tutulungan kaya siya nito? Paano siya makakalabas sa butas?,<i>Josephine Croser & Donna Gynell</i><br>Magkaibigan sina Pilandok at Unggoy. Nagtanim sila ng ilang puno ng saging upang pagsaluhan. Subalit hindi nagtagal ay hindi na sila naghahati at nag-aaway! Maghahati pa ba sila?,<i>Jill McDougall & Bill Wood</i><br>Si Lobo ay nakakita ng isang bagay na nakakatakot. Sinabihan niya si Oso na tumakbo. Ano ang nakakatakot? Makakalayo kaya sila mula rito?,<i>Amanda Graham & Greg Holfeld</i><br>Basa sa labas. Kailangan ng hari ng bagong bota. Ano ang gagawin niya?,<i>Jill McDougall & Bill Wood</i><br>Habang natutulog si Oso, si Lobo ay naghahanap ng pulot. Sinusundan niya ang mga langgam. Ipapakita ba ng mga langgam kay Lobo kung nasaan ang pulot? Makakakuha ba ng pulot si Oso?,<i>Amanda Graham & Greg Holfeld</i><br>Pumitas si Joan ng mga gulay sa kanyang hardin. Pumitas si Joan ng prutas sa kanyang hardin. Kakainin ba niya ang mga ito?,<i>Amanda Graham & Greg Holfeld</i><br>Si Joan ay may kambing noon, isang makulit, napakakulit na kambing. Kinain nito ang kanyang tinapay at damit at sabon. Kaya bang pigilan ni Joan ang kambing sa pagkain?,<i>Nigel Croser & Neil Curtis</i><br>Nakakita si Max ng mga hayop sa mga ulap. Nakakita sina Min at Mop ng mga hayop sa lupa. Pagkatapos ay nakakita sina Min at Mop ng isang soro sa lupa. Makukuha ba ng soro ang mga tupa?,<i>Nigel Croser & Neil Curtis</i><br>Si Max ay namasyal sa bukid. Ang gutom na soro ay sumusunod sa kanyang likuran. Mahuhuli kaya si Max?,<i>Nigel Croser & Neil Curtis</i><br>Ang malamig na gabi ay nagdala ng niyebe, kaya nagpadausdos si Max sa burol. Pagkatapos ay dumating ang napakalakas na hangin at nawala ang mga bibe. Ang lahat ng iba pang mga hayop ay naghanap. Mahahanap kaya nila ang mga bibe?,<i>Nigel Croser & Neil Curtis</i><br>Sina Max, Min at Mop ay pumunta sa perya. Nanalo ng premyo sina Min at Mop. Mananalo kaya ng premyo si Max?,<i>Nigel Croser & Neil Curtis</i><br>Mayroong yelo sa lawa. Kaya nag-skating si Max. Ngunit nakita ng soro si Max at plano nitong hulihin siya.,<i>Amanda Graham & Greg Holfeld</i><br>Sina Joan at Mick ay nakakita ng melon. Pareho nilang gusto ang melon para sa tsaa. Sino ang kakain sa melon?,<i>Bill Wood & Bill Wood</i><br>Gusto ni Oso na maglaro sa parke. Pero ayaw maglaro ni Lobo. Hanggang sa gusto ni Lobo na maglaro sa isang bagay. Makikipaglaro kaya sa kanya si Oso?,<i>Nigel Croser & Neil Curtis</i><br>Oras na ng paggugupit at hinabol ng asong tupa ang mga tupa. Mahahanap ba ng asong tupa si Max? Gugupitan ba ng manggugupit si Max?,<i>Nigel Croser & Neil Curtis</i><br>Hinabol ng asong tupa ang mga tupa. Kaya hinabol ni Max ang asong tupa. Ano ang mangyayari sa mga tupa? Pwede bang maging asong tupa si Max?,<i>Jill McDougall & Bill Wood</i><br>Sumakay sina Oso at Lobo sa isang troso sa putik. Si Oso ay naging maputik. Magiging maputik din ba si Lobo?,<i>Jill McDougall & Bill Wood</i><br>Binibigyan ni Lobo ng mga seresa si Oso. Ngunit si Lobo ay kumukuha ng higit pang seresa kapag hindi nakatingin si Oso. Sino ang makakakuha ng mas maraming seresa?,<i>Amanda Graham & Greg Holfeld</i><br>Nais ng hari na lumipad ng mataas. Paano siya lilipad? Makakalapag ba siya nang ligtas?,<i>Amanda Graham & Greg Holfeld</i><br>Nawala ng hari ang kanyang tsinelas. Tinulungan siya ng reyna na hanapin ang mga ito. Sa tingin mo, nasaan ang mga ito?,<i>Amanda Graham & Greg Holfeld</i><br>Sinusubukan ng reyna na matulog sa pamamagitan ng pagbibilang ng mga tupa, ngunit ang mga tupa ay dumaragdag lamang sa kanyang mga problema. Makakatulog pa kaya ang reyna?,<i>Amanda Graham & Greg Holfeld</i><br>Ang hari ay mahilig tumalon. Ang hari ay mahilig lumipad at umakyat. Ngunit may isang bagay na hindi gusto ng hari.,<i>Sarah Reynolds & Karl Saludar</i><br>Ang mabalahibong soro ay nagtatago. Bakit kaya? Basahin natin at alamin.,<i>Sarah Reynolds & Lance Patrick</i><br>Maaari kong pinturahan ang bakod ng pula, dilaw, at bughaw. Ano ang mangyayari pagkatapos?,<i>Sarah Reynolds & Karl Saludar</i><br>Gusto ko ang aking saranggola. Gusto ko ang aking bola. Pero ano ba talaga ang pinakagusto ko?,<i>Sarah Reynolds & Karl Saludar</i><br>Nakikita ko ang aking pamilya. Nakikita ko ang aking bayan. Ano pa ang makikita ng bata?,<i>Sarah Reynolds & Lance Patrick</i><br>Narito ang aking pamilya. Ano ang mangyayari kapag nakilala nila ang aking alagang hayop?,<i>Sarah Reynolds & Karl Saludar</i><br>Ang aking kamelyo ay kayang pumunta sa kaliwa, kanan, pataas at pababa. Ano ang hindi kayang gawin ng aking kamelyo?,<i>Sarah Reynolds & Lance Patrick</i><br>Ito ang aking kwarto. Ano ang kanyang makikita?,<i>Sarah Reynolds & Karl Saludar</i><br>Ang mga daga ay gusto kumain. Ano ang kanilang makikita?,<i>Sarah Reynolds & Lance Patrick</i><br>Ang itim na pusa ay tumatakbo. Ang itim na pusa ay tumatalon. Ano pa kayang gawin ng pusa?,<i>Sarah Reynolds & Lance Patrick</i><br>Ang babae ay abala ngayong araw. Ano ang ginawa niya?,<i>Sarah Reynolds & Lance Patrick</i><br>Tatlong bata ang pumunta sa panaderya. Ano kaya ang kukunin nila?,<i>Sarah Reynolds & Karl Saludar</i><br>Kailangan natin ng mansanas. Kailangan natin ng peras. Ano ang gagawin natin?,<i>Sarah Reynolds & Karl Saludar</i><br>Ang batang babae ay maraming gutom na kambing. Ilang kambing meron siya?,<i>Sarah Reynolds & Lance Patrick</i><br>Hinahanap ng batang lalaki ang kanyang sapatos. Saan kaya ito napunta?,<i>Sarah Reynolds & Karl Saludar</i><br>Ang batang babae ay gusto ng alagang hayop. Ano kaya ang kukunin niya?,<i>Sarah Reynolds & Lance Patrick</i><br>Ang batang babae ay gustong lumabas. Ano kaya ang susuotin niya ngayong malamig ang panahon?,<i>Sarah Reynolds & Lance Patrick</i><br>Isang batang lalaki ang gustong lumabas. Ano ang kanyang isusuot sa mainit na araw?,<i>Sarah Reynolds & Lance Patrick</i><br>Ang batang lalaki ay may mapa. Ano kaya ang makikita dito?,<i>Sarah Reynolds & Karl Saludar</i><br>Isang batang babae ang may robot. Ano ang kayang gawin nito? Basahin natin at alamin!,<i>Sarah Reynolds & Karl Saludar</i><br>Ang batang lalaki ay kayang tumakbo sa disyerto. Kaya niya tumakbo sa masukal gubat. Saan siya di makatakbo?,<i>Sarah Reynolds & Karl Saludar</i><br>May nakita ang isang batang babae na malaking kalat. Ano kaya ang makikita niya sa kalat na ito?,<i>Sarah Reynolds & Lance Patrick</i><br>May limang hugis ang panadero. Ano kaya ang magagawa niya sa mga ito?,<i>Josephine Croser & Lance Patrick</i><br>Puwede kang gumawa ng nakakatawang mukha. Ipapakita ng aklat na ito kung paano.,<i>Sarah Reynolds & Lance Patrick</i><br>Isang batang lalaki ang kayang gumawa ng mga maskara. Para kanino kaya ang mga ito?,<i>Sarah Reynolds & Karl Saludar</i><br>Ipinakita ng isang batang lalaki ang ginawa niya ngayong linggo. Ano ang mga ginawa niya?,<i>Josephine Croser & Lance Patrick</i><br>Nagpipinta ang mga bata ng mga numero. Bakit sila nagpipinta?,<i>Sarah Reynolds & Karl Saludar</i><br>May isang batang babae na nagsasalita tungkol sa panahon. Ano kaya ang mangyayari ngayong araw?,<i>Josephine Croser &amp; Lance Patrick</i> Ilang paraan ba ang meron para umakyat at bumaba? Tara, alamin natin!,<i>Sarah Reynolds & Karl Saludar</i><br>Panahon na para gumising. Ano ang ginawa ng batang lalaki ngayong araw?,<i>Sarah Reynolds & Karl Saludar</i><br>Gusto ng mga batang ito ang isports. Anong mga isports kaya ang kanilang lalaruin?');
    $published_dates = explode(',', '2021-02-19 04:32:47,2021-02-19 04:34:38,2021-02-19 04:35:29,2021-02-19 04:36:25,2021-02-19 04:36:56,2021-02-19 05:16:34,2021-02-19 05:17:06,2021-02-19 05:17:33,2021-02-19 05:24:20,2021-02-19 05:24:47,2022-09-13 03:55:25,2022-09-13 03:55:26,2022-09-13 03:55:26,2022-09-13 03:55:26,2022-09-13 03:55:26,2022-09-13 03:55:26,2022-09-13 03:55:26,2022-09-13 03:55:26,2022-09-13 03:55:26,2022-09-13 03:55:27,2022-09-13 03:55:27,2022-09-13 03:55:27,2022-09-13 03:55:27,2022-09-13 03:55:27,2022-09-13 03:55:28,2022-09-13 03:55:28,2022-09-13 03:55:28,2022-09-13 03:55:28,2022-09-13 03:55:28,2022-09-13 03:55:28,2023-04-04 06:53:13,2023-04-04 06:53:13,2023-04-04 06:53:13,2023-04-04 06:53:13,2023-04-04 06:53:13,2023-04-04 06:53:13,2023-04-04 06:53:14,2023-04-04 06:53:14,2023-04-04 06:53:14,2023-04-04 06:53:14,2023-04-04 06:53:14,2023-04-04 06:53:14,2023-04-04 06:53:14,2023-04-04 06:53:15,2023-04-04 06:53:15,2023-04-04 06:53:15,2023-04-04 06:53:15,2023-04-04 06:53:15,2023-04-04 06:53:15,2023-04-04 06:53:15,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2024-05-14 06:44:27,2025-05-08 01:03:00,2025-05-08 01:03:08,2025-05-08 01:03:16,2025-05-08 01:03:24,2025-05-08 01:03:32,2025-05-08 01:03:39,2025-05-08 01:03:47,2025-05-08 01:03:55,2025-05-08 01:03:55,2025-05-08 01:03:56,2025-11-05 01:15:19,2025-11-05 01:15:28,2025-11-05 01:15:36,2025-11-05 01:15:45,2025-11-05 01:15:53,2025-11-05 01:16:02,2025-11-05 01:16:11,2025-11-05 01:16:19,2025-11-05 01:16:28,2025-11-05 01:16:37');

    foreach ($titles as $index => $title) {

        $date = $published_dates[$index]; 
        $desc = $descriptions[$index]; 

        // echo $date . ' | ' . $title . ' | ' . $desc . '<br/>';

        wp_insert_post([
            'post_title'  => trim($title),
            'post_type'   => 'video',
            'post_status' => 'publish',
            'post_date'      => $date, 
            'post_date_gmt'  => get_gmt_from_date($date),
            'meta_input'  => [
                'description' => $desc,
                'language' => 101,
            ],
        ]);

    }

    exit('Inserted!');
});
*/

/**
 * ----------------------------------------------------------------
 * Quick insert of new titles (to be deleted when not needed)
 * ----------------------------------------------------------------
 */
/*
add_action('init', function () {

    if (!isset($_GET['seed_stories'])) return; // run only with URL trigger

    // use http://lote4kids.local/?seed_stories=1

    $titles = explode(',', "Birds are Amazing,Emergency Vehicles,Follow Me!,Good morning,How are you?,How many sleeps?,I can jump,I know some Māori words,Introduce yourself,Let's Go!,Māori Gods,Matariki,My Pet,Outside fun,Puanga and Matariki,Remembering,Shapes,The Journey,The Matariki Star Cluster,The night has arrived,The spirit of Waitangi,Tidy up time,Wait, my friend!,We love fruit ,What is this?,Where's my hat?,Who do these belong to?,Work together");

    foreach ($titles as $index => $title) {

        wp_insert_post([
            'post_title'  => trim($title),
            'post_type'   => 'story',
            'post_status' => 'publish'
        ]);

    }

    exit('Inserted!');
});
*/

/**
 * ----------------------------------------------------------------
 * Populate library dashboard and welcome logos
 * ----------------------------------------------------------------
 */
/*
add_action('init', function () {

    if (!isset($_GET['seed_libraries'])) return; // run only with URL trigger

	$libraries = [
	    "Algonquin Area Public Library",
	    "Allen County Public Library",
	    "Allen Public Library",
	    "Alsip-Merrionette Park Public Library District",
	    "Anderson Public Library",
	    "Ankeny Kirkendall Public Library",
	    "Ann Arbor District Library",
	    "Anne West Lindsey District Library",
	    "Arapahoe Libraries",
	    "Arlington Heights Memorial Library",
	    "Ascension Parish Library",
	    "Ashburton Public Library",
	    "Auburn Hills Public Library",
	    "Aurora Public Library",
	    "Aurora Public Library District",
	    "Aurukun Shire Council",
	    "Baker County",
	    "Baldwin Public Library",
	    "Balonne Shire Council",
	    "Banana Shire Council",
	    "Barcaldine Regional Council",
	    "Barcoo Shire Council",
	    "Barrington Area Library",
	    "Bartholomew County Public Library",
	    "Bartlett Public Library District",
	    "Bassendean Memorial Public Library",
	    "Batavia Public Library District",
	    "Bayside City Council (VIC)",
	    "Bayside Council Libraries (NSW)",
	    "BC Libraries Cooperative",
	    "Beauregard Parish Library",
	    "Bellmore Memorial Library",
	    "Bensenville Community Public Library",
	    "Berkeley Heights Public Library",
	    "Bernards Township Library",
	    "Bernardsville Public Library",
	    "Berwyn Public Library",
	    "Bethpage Public Library",
	    "Bettendorf Public Library",
	    "Bexley Public Library",
	    "Bindoon Public Library",
	    "Blackall–Tambo RC",
	    "Blacktown City Libraries",
	    "Bloomfield Township Public Library",
	    "Boise Public Library",
	    "Boonton Holmes Public Library",
	    "Boston Public Library",
	    "Boulia Shire Council",
	    "Boyden Library",
	    "Bracebridge Public Library",
	    "Bradford West Gwillimbury Public Library",
	    "Bridgeview Public Library",
	    "Brimbank City Council",
	    "Bulloo Shire Council",
	    "Bundaberg Regional Council",
	    "Burdekin Shire Council",
	    "Burke Shire Council",
	    "Burlingame Public Library",
	    "Burlington County Library System",
	    "Burlington Public Library",
	    "Butler Public Library",
	    "Cairns Regional Council",
	    "Calderdale Council Libraries",
	    "Caledon Public Library",
	    "Calgary Public Library",
	    "Califa",
	    "Camarillo Public Library",
	    "Camden Libraries (AU)",
	    "Campbelltown City Council",
	    "Capel Public Library",
	    "Carlow County Council",
	    "Carmel Clay Public Library",
	    "Carol Stream Public Library",
	    "Carpentaria Shire Council",
	    "Carroll County Public Library",
	    "Cass County Public Library",
	    "Cassowary Coast Regional Council",
	    "Castlegar and District Public Library",
	    "Catawba County Library",
	    "Catholic Schools Parramatta Diocese",
	    "Cavan County Council Libraries",
	    "Cedar Park Public Library",
	    "Central Highlands Regional Council",
	    "Central Otago District Libraries",
	    "Central Rappahannock Regional Library",
	    "Cerritos Library",
	    "Champaign Public Library",
	    "Charters Towers Regional Council",
	    "Cherbourg Aboriginal Shire",
	    "Chesapeake Public Library",
	    "Chester Public Library",
	    "Christchurch City Libraries",
	    "City Libraries Townsville",
	    "City of Albany",
	    "City of Armadale",
	    "City of Bayswater",
	    "City of Bunbury",
	    "City of Busselton Libraries",
	    "City of Canada Bay",
	    "City of Canning",
	    "City of Canterbury Bankstown",
	    "City of Fremantle",
	    "City of Gosnells",
	    "City of Greater Geraldton",
	    "City of Joondalup Libraries",
	    "City of Kalamunda",
	    "City of Karratha",
	    "City of Kwinana",
	    "City of Mandurah",
	    "City of Marion",
	    "City of Melbourne",
	    "City of Melville",
	    "City of Monash",
	    "City of Moonee Valley",
	    "City of Nedlands",
	    "City of Perth",
	    "City of Port Adelaide Enfield Libraries",
	    "City of Rockingham",
	    "City of Ryde",
	    "City of South Perth",
	    "City of Stirling",
	    "City of Subiaco",
	    "City of Swan",
	    "City of Vincent Library",
	    "City of Wanneroo",
	    "Clare County Library",
	    "Claremont Community Hub and Library",
	    "Clearview Public Library",
	    "Clifton Park-Halfmoon Public Library",
	    "Clifton Public Library",
	    "Cloncurry Shire Council",
	    "Clutha District Libraries",
	    "Cockburn Libraries",
	    "Collingwood Public Library",
	    "Colusa County Library",
	    "Concord Public Library",
	    "Connected Libraries",
	    "Cook Shire Council",
	    "Cork County Council",
	    "Cornwall Libraries",
	    "Cornwall Public Library",
	    "County of Brant Public Library",
	    "Cowlishaw Elementary School",
	    "Craighead County Jonesboro Public Library",
	    "Cromaine District Library",
	    "Croydon Shire Council",
	    "Cumberland City Council",
	    "Cybrarium Homestead",
	    "Daniel Boone Regional Library",
	    "Darebin Libraries",
	    "Daviess County Public Library",
	    "Dearborn Heights City Libraries",
	    "Decommissioned - Anchorage Public Library",
	    "DeForest Area Public Library",
	    "Dekalb Public Library",
	    "Denville Public Library",
	    "Des Plaines Public Library",
	    "DeSoto Parish Library",
	    "Diamantina Shire Council",
	    "Digital Content Associates (DCA)",
	    "Digital Library",
	    "Digitales",
	    "DLR Libraries (Dún Laoghaire-Rathdown County)",
	    "Donegal Library Services",
	    "Douglas Shire Council",
	    "Dover Public Library",
	    "Downers Grove Public Library",
	    "Dublin City Council",
	    "Dumbleyung Public Library & Kukerin Public Library",
	    "Dunedin Public Libraries",
	    "Durham County Libraries"
	];

    /*
	$libraries = [
	    "Eagle Valley Library District",
	    "East Baton Rouge Parish Library",
	    "East Central Arkansas Regional Library",
	    "East Gwillimbury Public Library",
	    "East Hanover Public Library",
	    "East Meadow Public Library",
	    "Eastern Regional Libraries",
	    "Edgerton Community Elementary",
	    "Ela Area Public Library",
	    "Elk Grove Village Public Library",
	    "Elmont Public Library",
	    "EPIC Libraries",
	    "Essa Public Library",
	    "Essex County Library",
	    "Etheridge Shire Council",
	    "Evanston Public Library",
	    "Fairfield City Libraries",
	    "Fairfield Free Public Library",
	    "Falmouth Public Library",
	    "Farmingdale Public Library",
	    "Farmington Community Library",
	    "Fingal County Council",
	    "Flinders Shire Council",
	    "Florham Park Public Library",
	    "Flower Memorial Library",
	    "Forest Hill Library District",
	    "Forsyth County Public Library",
	    "Fort Worth Independent School District",
	    "Frankland River Public Library",
	    "Franklin Park Public Library",
	    "Franklin Public School District",
	    "Frankston Libraries",
	    "Fraser Coast Regional Council",
	    "Fraser Lake Public Library",
	    "Fremont Public Library District",
	    "Gail Borden Public Library District",
	    "Galway County Library",
	    "Garden City Public Library",
	    "Garfield County Library",
	    "Georges River Council",
	    "Gibsons and District Public Library",
	    "Gladstone Regional Council",
	    "Glen Cove Public Library",
	    "Glen Education",
	    "Glen Eira Libraries",
	    "Glen Ellyn Public Library",
	    "Glenside Public Library District",
	    "Gold Coast City Council",
	    "Goldfields Library Corporation",
	    "Goondiwindi RC",
	    "Goshen Public Library",
	    "Goulburn Valley Libraries",
	    "Grand Valley Public Library",
	    "Grayslake Area Public Library",
	    "Greater Dandenong Libraries",
	    "Gunnedah Shire Library",
	    "Gympie Regional Council",
	    "Hackettstown Public Library",
	    "Hall County Library System",
	    "Hamtramck Public Library",
	    "Harvest Intermediate School (DeForest Area SD)",
	    "Hastings District Libraries",
	    "Haverhill Public Library",
	    "Hayward Public Library",
	    "HB Williams Memorial Library",
	    "Helen Plum Memorial Public Library District",
	    "Hempstead Public Library",
	    "Henry Waldinger Memorial Library",
	    "Herrick District Library",
	    "Hewlett-Woodmere Public Library",
	    "High Point Public Library",
	    "Highland Park Public Library",
	    "Hillside Public Library",
	    "Hinchinbrook Shire Council",
	    "Hobsons Bay Libraries",
	    "Hope Vale Aboriginal Shire Council",
	    "Horowhenua District Council Library",
	    "Hunterdon County Library System",
	    "Huntley Area Public Library",
	    "Hurst Public Library",
	    "Hussey-Mayfield Memorial Public Library",
	    "Hutt City Library",
	    "Hyphen - Wodonga Library",
	    "ILA 2025 Annual Conference",
	    "Illinois Heartland Library System",
	    "Illinois Youth Services Institute (IYSI)",
	    "Indian Prairie Public Library",
	    "Indian Trails Public Library District",
	    "Innisfil Public Library",
	    "International School Hannover Region (ISHR)",
	    "Ipswich Libraries",
	    "Isaac Region",
	    "Island Park Public Library",
	    "Islington Libraries",
	    "Jefferson County Library District",
	    "Jefferson Madison Regional Library (JMRL)",
	    "Jefferson Public Library",
	    "Jeffersonville Township Public Library",
	    "Jericho Public Library",
	    "Jersey Library",
	    "Johnson County Public Library",
	    "Johnston Public Library",
	    "Joplin Public Library",
	    "Katherine Delmar Burke School",
	    "Kegonsa Elementary School",
	    "Kemmerer Library (Harding)",
	    "Kerry Library",
	    "Kewanee Public Library District",
	    "Kewaskum School District",
	    "Kildare County Council Libraries",
	    "King County Library System",
	    "Kingston Libraries",
	    "Kingston Libraries (UK)",
	    "Kinnelon Public Library",
	    "Kitchener Public Library",
	    "Koala Kids Foundation",
	    "Kununurra School & Community Library",
	    "La Grange Public Library",
	    "Lancaster Public Library",
	    "Lane Libraries",
	    "Laois County Library Services",
	    "Las Cruces Libraries",
	    "Las Vegas-Clark County Library District",
	    "Leitrim County Council Libraries",
	    "Levittown Public Library",
	    "Lewisville Public Library",
	    "Libraries ACT",
	    "Libraries Tasmania",
	    "Library of the Chathams",
	    "Lincoln Parish Library",
	    "Lincoln Park Public Library",
	    "Lincolnwood Public Library",
	    "Linda Sokol Francis Brookfield Library",
	    "Linden Free Public Library",
	    "Lindenhurst Memorial Library",
	    "Live Oak Public Libraries",
	    "Livermore Public Library",
	    "Liverpool City Libraries (UK)",
	    "Liverpool City Library",
	    "Liverpool Public Library",
	    "Livingstone Shire Council",
	    "Lockhart River Aboriginal Shire Council",
	    "Lockyer Valley Regional Council",
	    "Locust Valley Library",
	    "Logan City Council",
	    "Logan Middle School and Longfellow Middle School",
	    "London Borough of Hammersmith and Fulham",
	    "Long Beach Public Library",
	    "Long Hill Township Public Library",
	    "Longford County Council",
	    "Longreach Regional Council",
	    "Longwood Public Library",
	    "Loudoun County Public Library",
	    "Louth County Council",
	    "Lucy Robbins Welles Library",
	    "Luton Libraries",
	    "Mackay Regional Libraries",
	    "Madison Public Library",
	    "Main Alliance",
	    "Manhasset Public Library",
	    "Manhattan Public Library",
	    "Mapoon Aboriginal Shire Council",
	    "Maranoa Regional Council",
	    "Mareeba Shire Council",
	    "Maribyrnong City Council",
	    "Marin County Free Library",
	    "Marion Public Library",
	    "Marlborough District Libraries",
	    "Marshall County Memorial Library",
	    "Mary Immaculate College",
	    "Mary Lib Saleh Euless Public Library",
	    "Mason Public Library",
	    "Masterton District Library",
	    "Matteson Area Public Library District",
	    "Mattituck Jr Sr High School",
	    "McAuley Library / Mercy College - Koondoola",
	    "McHenry Public Library District",
	    "McKinlay Shire Council",
	    "Meath County Council",
	    "Medford Public Library",
	    "Melton City Libraries",
	    "Menasha Joint School District",
	    "Mendham Borough Library",
	    "Mendham Township Public Library",
	    "Menlo Park Library",
	    "Menzies Public Library",
	    "Mercer County Library",
	    "Merri-bek Libraries / Moreland City Libraries Libraries",
	    "Metropolitan Library System",
	    "Mid North Coast Library Co-Operative",
	    "Middle Country Public Library",
	    "Middleborough Public Library",
	    "Middletown Public Library",
	    "Midland Public Library",
	    "Midlothian Libraries",
	    "Mildura Rural City Council",
	    "Mitchell Shire Council",
	    "Mobius Libraries",
	    "Moline Public Library",
	    "Monaghan County Council Libraries",
	    "Monroe County Library System",
	    "Monroe County Public Library",
	    "Monroeville Public Library",
	    "Montville Public Library",
	    "Moreton Bay Regional Council",
	    "Mornington Peninsula Shire Library Service",
	    "Morris County Library",
	    "Morris Plains Public Library",
	    "Morrison-Talbott Library",
	    "Morristown-Morris Township",
	    "Morton Grove Public Library",
	    "Mount Arlington Public Library",
	    "Mount Gambier Library",
	    "Mount Isa City Council",
	    "Mount Olive Public Library",
	    "Mount Prospect Public Library",
	    "Mountain Lakes Public Library",
	    "Mountainside Public Library",
	    "Murweh Shire Council",
	    "Myli",
	    "Naperville Public Library",
	    "Napranum Aboriginal Shire Council",
	    "Nashua Public Library",
	    "Nassau Library System",
	    "Nevins Library",
	    "New Braunfels Public Library",
	    "New City Library",
	    "New Hanover County Public Library",
	    "New Lenox Public Library",
	    "New Providence Memorial Library",
	    "New Tecumseth Public Library",
	    "Newmarket Public Library",
	    "NextSense",
	    "Niles-Maine District Library",
	    "Nixa Public Schools",
	    "Noosa Council",
	    "Norfolk County Public Library",
	    "Normal Public Library",
	    "North Bellmore Public Library",
	    "North Burnett Regional Council",
	    "North Canton Public Library",
	    "North Chicago Public Library",
	    "North Miami Beach Public Library",
	    "Northbrook Public Library",
	    "Northeast Ohio Regional Library System",
	    "Northern Peninsula Area Regional Council",
	    "Northland Public Library",
	    "Norwalk Public Library",
	    "NYLA 2025 Annual Conference & Trade Show",
	    "NZ Demo Library",
	    "Oak Lawn Public Library",
	    "Oak Ridge Public Library",
	    "Oceanside Library",
	    "Offaly County Council",
	    "Ontario Library Service (OLS)",
	    "Orange County Library System",
	    "Orland Park Public Library",
	    "Osceola Library System",
	    "Oswego Public Library District",
	    "Owen Sound & North Grey Union Public Library",
	    "Palatine Public Library District",
	    "Palm Beach County Library System",
	    "Palm Island Aboriginal Shire Council",
	    "Palmerston North City Library",
	    "Palo Alto City Library",
	    "Palos Park Public Library",
	    "Park House English School",
	    "Park Ridge Public Library",
	    "Paroo Shire Council",
	    "Parsippany Library",
	    "Pasadena Public Library",
	    "Peabody Institute Library of Danvers",
	    "Pendleton Community Public Library",
	    "Penetanguishene Public Library",
	    "Penrith City Library",
	    "Peoria Public Library",
	    "Pequannock Public Library",
	    "Pflugerville Public Library",
	    "Pierce County Library System",
	    "Pinellas Public Library Cooperative (PPLC)",
	    "Pioneerland Library System",
	    "Plainfield Public Library District",
	    "Plainview-Old Bethpage Public Library",
	    "Plum Creek Library System",
	    "Plymouth District Library",
	    "Pormpuraaw Aboriginal Shire Council",
	    "Port Washington Public Library",
	    "Portsmouth Libraries and Archives",
	    "Poudre Libraries",
	    "Prairie Trails Public Library District",
	    "Prospect Heights Public Library",
	    "Public Libraries Association (PLA)",
	    "Queenstown Lakes District Council",
	    "Quilpie Shire Council",
	    "Radnor Memorial Library",
	    "RAILS",
	    "Ramara Township Public Library",
	    "Randolph Township Free Public Library",
	    "Randwick City Library",
	    "Raritan Public Library",
	    "Reading Public Library",
	    "Red Wing Public Library",
	    "Reddick Public Library District",
	    "Redland City Council",
	    "Redwood City Public Library",
	    "Region of Waterloo Library",
	    "Richland Public Library",
	    "Richmond Public Library",
	    "Richmond Shire Council",
	    "Ridgefield Free Public Library",
	    "River Trails SD 26",
	    "Riverdale Public Library",
	    "Riverdale Public Library District",
	    "Riverside County Library System",
	    "Rockaway Borough Library",
	    "Rockaway Township Public Library",
	    "Rockhampton Regional Council",
	    "Rogers Memorial Library",
	    "Rogers Public Library",
	    "Rolling Meadows Library",
	    "Roosevelt Public Library",
	    "Roscommon County Council Libraries",
	    "Roxbury Public Library",
	    "Rural Libraries Queensland and Indigenous Knowledge Centres",
	    "Ruth Faulkner Library",
	    "Rutherford County Library System",
	    "Sachem Public Library",
	    "Saline District Library",
	    "San Diego Public Library",
	    "San José Public Library",
	    "Sandhill Regional Library",
	    "Santa Ana Public Library",
	    "Santa Clara City Library",
	    "Santa Fe Springs City Library",
	    "Satilla Regional Library System",
	    "Scenic Rim Regional Council",
	    "Schaumburg Township District Library",
	    "Schiller Park Public Library",
	    "School District of Black Hawk",
	    "School District U-46",
	    "SEFLIN Annual Conference 2025",
	    "Sefton Council Libraries",
	    "Seguin Public Libraries",
	    "Severn Public Library",
	    "Shelter Rock Public Library",
	    "Shenendehowa Central Schools",
	    "Shire of Ashburton",
	    "Shire of Augusta Margaret River Libraries",
	    "Shire of Beverley",
	    "Shire of Boddington",
	    "Shire of Bridgetown-Greenbushes",
	    "Shire of Broome",
	    "Shire of Carnarvon",
	    "Shire of Coolgardie",
	    "Shire of Dardanup",
	    "Shire of Denmark",
	    "Shire of Donnybrook Balingup",
	    "Shire of East Pilbara",
	    "Shire of Esperance",
	    "Shire of Gingin",
	    "Shire of Gnowangerup",
	    "Shire of Harvey",
	    "Shire of Jerramungup",
	    "Shire of Kojonup",
	    "Shire of Koorda",
	    "Shire of Manjimup",
	    "Shire of Merredin",
	    "Shire of Moora",
	    "Shire of Mundaring",
	    "Shire of Murray",
	    "Shire of Narrogin",
	    "Shire of Northam",
	    "Shire of Plantagenet",
	    "Shire of Ravensthorpe",
	    "Shire of Serpentine Jarrahdale",
	    "Shire of Waroona",
	    "Shire of Yilgarn",
	    "Shrewsbury Public Library",
	    "Shrewsbury Public Library - backup",
	    "Siloam Springs Public Library",
	    "Simcoe County Cooperative",
	    "Sioux Lookout Public Library",
	    "Skokie Public Library",
	    "Sno-Isle Libraries",
	    "Solano County Library",
	    "Somerset College",
	    "Somerset County Library",
	    "Somerset County Library System of New Jersey",
	    "Somerset Regional Council",
	    "South Burnett Regional Council",
	    "South Dublin County Council",
	    "South Holland Public Library",
	    "South Pasadena Public Library",
	    "South Plainfield Public Library",
	    "Southern Downs Regional Council",
	    "Sparta Public Library",
	    "Spokane Public Library Library",
	    "Springwater Public Library",
	    "St Louis County Library",
	    "St. Albert Public Library",
	    "St. Clair County Library System",
	    "St. Joseph County Public Library",
	    "Stanton Library - North Sydney Council",
	    "State Library of Queensland",
	    "State Library of Western Australia",
	    "Stockton-San Joaquin County Public Library",
	    "Stoughton Area School District Library",
	    "Stoughton Public Library",
	    "Stratford Public Library",
	    "Sugar Grove Public Library",
	    "Summit Free Public Library",
	    "Sumter Public Library",
	    "Sun Prairie Public Library",
	    "Sunshine Coast Regional Council",
	    "Surrey Libraries",
	    "Sussex County Library",
	    "Sutherland Shire Council",
	    "Sutton Libraries",
	    "Swan Hill Regional Library",
	    "Syosset Public Library",
	    "Tablelands Regional Council",
	    "Tasman District Libraries",
	    "Tay Public Library",
	    "Taylor Public Library",
	    "Tenn-Share",
	    "The Alberta Library (TAL)",
	    "The Bryant Library",
	    "The Colony Public Library",
	    "The Edge",
	    "The Grove Library",
	    "The Indianapolis Public Library",
	    "The Library Network (TLN)",
	    "The Library of Hattiesburg, Petal & Forrest County",
	    "The Public Library of Brookline",
	    "The Scots College",
	    "Thomas Crane Public Library",
	    "Timaru District Libraries",
	    "Toodyay Public Library & Morangup Public Library",
	    "Toowoomba Regional Council",
	    "Torres Shire Council",
	    "Torres Strait Island Regional Council",
	    "Town of Cambridge",
	    "Town of Port Hedland",
	    "Town of Victoria Park",
	    "Traverse des Sioux Library System",
	    "Trenton Free Public Library",
	    "Troy Public Library",
	    "Twin Falls Public Library",
	    "Tye Preston Memorial Library",
	    "Tyler Public Library",
	    "Uniondale Public Library",
	    "Upper Hutt Libraries",
	    "Vancouver Public Library",
	    "Vaughan Public Libraries",
	    "Vernon Area Public Library District",
	    "Wagin Library & Gallery",
	    "Waimakariri District Libraries",
	    "Warren County Library System",
	    "Warren-Newport Public Library",
	    "Wasaga Beach Public Library",
	    "Washington Township Public Library",
	    "Washington-Centerville Public Library",
	    "Waterloo Public Library",
	    "Waukegan Public Library",
	    "Waukesha Public Library",
	    "Waunakee Community School District",
	    "Waunakee Intermediate School",
	    "Weipa Town Authority",
	    "Wellington City Libraries",
	    "West Chicago Public Library District",
	    "Westbank Libraries",
	    "Western District Library",
	    "Western Downs Regional Council",
	    "Westmeath County Council",
	    "Wexford County Council",
	    "Wharton Public Library",
	    "Wheeling Community Consolidated SD 21 (CCSD21)",
	    "Whippanong Library",
	    "Whitsunday Regional Council",
	    "Wicklow County Council Libraries",
	    "Williams Public Library",
	    "Willoughby-Eastlake Public Library",
	    "Windsor Public Library",
	    "Winton Shire Council",
	    "Wollongong City Libraries",
	    "Wood Buffalo Regional Library",
	    "Wood Dale Public Library",
	    "Woodridge Public Library",
	    "Woorabinda Aboriginal Shire Council",
	    "Wujal Wujal Aboriginal Shire Council",
	    "Wyndham City Libraries",
	    "Yarra Libraries",
	    "Yarra Plenty Regional Library",
	    "Yarrabah Aboriginal Shire Council",
	    "Yellowhead Regional Library",
	    "York County Library",
	    "Your Library"
	];
	*/

	/*
	// API endpoint
	$apiUrl = 'https://lote4kids.com/wp-json/v2/login/libraries';

	// Fetch data from the API
	$response = file_get_contents($apiUrl);

	// Check if the request was successful
	if ($response === false) {
	    die('Error fetching data from API');
	}

	// Decode JSON response
	$apiLibraries = json_decode($response, true);

	// Check if JSON decoding was successful
	if ($apiLibraries === null) {
	    die('Error decoding JSON data');
	}

	// Create a lookup array for faster searching (title => banner)
	$apiLookup = [];
	foreach ($apiLibraries as $library) {
	    $apiLookup[$library['title']] = $library['banner'] ?? '';
	}

	// Search for each library and display banner
	foreach ($libraries as $libraryName) {
	    // echo "<strong>" . htmlspecialchars($libraryName) . ":</strong> ";
	    
	    if (isset($apiLookup[$libraryName]) && !empty($apiLookup[$libraryName])) {
	        echo htmlspecialchars($apiLookup[$libraryName]);
	    } else {
	        echo "None";
	    }
	    
	    echo "<br>\n";
	}

	exit;

});

// -----------------------------------------------
// Languages Flag Image Admin Page
// -----------------------------------------------

add_action( 'admin_menu', 'lote_languages_flag_menu' );

function lote_languages_flag_menu() {
    add_menu_page(
        'Languages Flag Images',
        'Languages Flags',
        'manage_options',
        'languages-flag-images',
        'lote_languages_flag_page',
        'dashicons-flag',
        30
    );
}

function lote_languages_flag_page() {
    global $wpdb;

    $results = $wpdb->get_results( "
        SELECT p.ID, p.post_title, pm.meta_value AS flag_mobile_image
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm 
            ON p.ID = pm.post_id 
            AND pm.meta_key = 'lang_flag_mobile_image'
        WHERE p.post_type = 'language'
          AND p.post_status = 'publish'
        ORDER BY p.post_title ASC
    " );

    ?>
    <div class="wrap">
        <h1>Languages — Flag Mobile Images</h1>

        <?php if ( empty( $results ) ) : ?>
            <p>No published languages found.</p>
        <?php else : ?>

        <style>
            #lang-flag-table { border-collapse: collapse; width: 100%; max-width: 700px; }
            #lang-flag-table th, #lang-flag-table td { padding: 10px 14px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
            #lang-flag-table th { background: #f0f0f1; font-weight: 600; }
            #lang-flag-table tr:nth-child(even) { background: #f9f9f9; }
            #lang-flag-table .no-image { color: #999; font-style: italic; }
            .lang-count { margin-bottom: 12px; color: #555; }
        </style>

        <p class="lang-count">Showing <strong><?php echo count( $results ); ?></strong> languages.</p>

        <table id="lang-flag-table">
            <thead>
                <tr>
                    <th>Language</th>
                    <th>Flag URL</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $results as $row ) :
                $img_url = '';

                if ( is_numeric( $row->flag_mobile_image ) && intval( $row->flag_mobile_image ) > 0 ) {
                    $img_url = wp_get_attachment_image_url( $row->flag_mobile_image, 'full' );
                } elseif ( ! empty( $row->flag_mobile_image ) ) {
                    $img_url = $row->flag_mobile_image;
                }
            ?>
                <tr>
                    <td><?php echo esc_html( $row->post_title ); ?></td>
                    <td>
                        <?php if ( $img_url ) : ?>
                            <?php echo esc_html( $img_url ); ?>
                        <?php else : ?>
                            <span class="no-image">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>
    </div>
    <?php
}
*/

/**
 * Library Missing Logos Admin Page
 * Add this to your theme's functions.php or a custom plugin
 */
/*
add_action( 'admin_menu', 'lote_missing_logos_menu' );

function lote_missing_logos_menu() {
    add_menu_page(
        'Library Missing Logos',
        'Missing Logos',
        'manage_options',
        'lote-missing-logos',
        'lote_missing_logos_page',
        'dashicons-format-image',
        80
    );
}

function lote_missing_logos_page() {
    $response = wp_remote_get( 'https://lote4kids.com/endpoints/all-libraries/', [
        'timeout' => 30,
    ] );

    echo '<div class="wrap">';
    echo '<h1>Libraries with Missing Logos</h1>';

    if ( is_wp_error( $response ) ) {
        echo '<div class="notice notice-error"><p>Error fetching data: ' . esc_html( $response->get_error_message() ) . '</p></div>';
        echo '</div>';
        return;
    }

    $body = wp_remote_retrieve_body( $response );
    $libraries = json_decode( $body, true );

    if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $libraries ) ) {
        echo '<div class="notice notice-error"><p>Invalid JSON response from endpoint.</p></div>';
        echo '</div>';
        return;
    }

    $missing = array_filter( $libraries, fn( $lib ) => empty( $lib['logo'] ) );

    if ( empty( $missing ) ) {
        echo '<div class="notice notice-success"><p>All libraries have logos!</p></div>';
        echo '</div>';
        return;
    }

    echo '<p>Found <strong>' . count( $missing ) . '</strong> libraries with no logo.</p>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Title</th></tr></thead>';
    echo '<tbody>';

    foreach ( $missing as $lib ) {
        $title = ! empty( $lib['title'] ) ? esc_html( $lib['title'] ) : '(no title)';
        echo '<tr><td>' . $title . '</td></tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}
*/
/*
add_action( 'template_redirect', function () {
    if ( ! isset( $_GET['check-english-us'] ) ) {
        return;
    }

    $query = new WP_Query( [
        'post_type'      => 'book',
        'post_status'    => 'publish',
        'posts_per_page' => -1,

        'meta_query'     => [
            'relation' => 'AND',
            [
                'key'     => 'language',
                'value'   => '127598',
                'compare' => '=',
            ],
            [
                'key'     => 'book_type',
                'value'   => 'flipbook',
                'compare' => '=',
            ],
        ],
    ] );

    echo '<h2>Books — language: 127598 / book_type: flipbook</h2>';
    echo '<p>Found: ' . $query->found_posts . ' post(s)</p>';

    if ( $query->have_posts() ) {
        // First pass: collect data and find the max number of repeater rows
        $rows      = [];
        $max_pages = 0;

        while ( $query->have_posts() ) {
            $query->the_post();
            $id    = get_the_ID();
            $count = (int) get_post_meta( $id, 'audio', true );
            $pages = [];

            for ( $i = 0; $i < $count; $i++ ) {
                $pages[] = [
                    'page_number' => get_post_meta( $id, 'audio_' . $i . '_page_number', true ),
                    'audio_file'  => get_post_meta( $id, 'audio_' . $i . '_audio_file', true ),
                ];
            }

            // Find the last index that actually has a page_number value
            $last_real_index = -1;
            for ( $j = 0; $j < $count; $j++ ) {
                if ( isset( $pages[ $j ] ) && $pages[ $j ]['page_number'] !== '' ) {
                    $last_real_index = $j;
                }
            }

            // Fill in any missing/blank rows up to $count
            if ( $last_real_index >= 0 ) {
                $last_page = (int) $pages[ $last_real_index ]['page_number'];
                $last_file = $pages[ $last_real_index ]['audio_file'];

                preg_match( '/(\d+)(\.\w+)$/', $last_file, $m );
                $file_num  = isset( $m[1] ) ? (int) $m[1] : null;
                $file_ext  = $m[2] ?? '';
                $file_base = $file_num !== null ? preg_replace( '/\d+(\.\w+)$/', '', $last_file ) : $last_file;

                for ( $j = $last_real_index + 1; $j < $count; $j++ ) {
                    $last_page++;
                    $new_file = $file_num !== null
                        ? $file_base . ++$file_num . $file_ext
                        : $last_file;
                    $pages[ $j ] = [
                        'page_number' => $last_page,
                        'audio_file'  => $new_file,
                    ];
                }
            }

            $max_pages = max( $max_pages, $count );
            $rows[]    = [
                'title' => get_the_title(),
                'date'  => get_the_date( 'Y-m-d G:i:s' ),
                'audio' => $count,
                'pages' => $pages,
            ];
        }

        wp_reset_postdata();

        // Sort alphabetically by title, normalizing dashes for consistent comparison
        usort( $rows, function( $a, $b ) {
            $normalize = fn( $s ) => strtolower( str_replace( [ '—', '–', '−' ], '-', $s ) );
            return strcmp( $normalize( $a['title'] ), $normalize( $b['title'] ) );
        } );

        // Build table header
        echo '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">';
        echo '<thead><tr><th>Post Title</th><th>Date</th><th>audio</th>';
        for ( $i = 0; $i < $max_pages; $i++ ) {
            echo '<th>audio_' . $i . '_audio_file</th>';
            echo '<th>audio_' . $i . '_page_number</th>';
        }
        echo '</tr></thead><tbody>';

        // Build rows
        foreach ( $rows as $row ) {
            echo '<tr style="white-space:nowrap;">';
            $title = str_replace( [ '&#8211;', '&#8212;', '&ndash;', '&mdash;' ], '-', $row['title'] );
            $title = preg_replace( '/[\x{2010}-\x{2015}\x{2212}\x{FE58}\x{FE63}\x{FF0D}]/u', '-', $title );
            echo '<td>' . $title . '</td>';
            echo '<td>' . esc_html( $row['date'] ) . '</td>';
            echo '<td>' . esc_html( $row['audio'] ) . '</td>';
            for ( $i = 0; $i < $max_pages; $i++ ) {
                $page_number = $row['pages'][ $i ]['page_number'] ?? '';
                $audio_file  = isset( $row['pages'][ $i ]['audio_file'] )
                    ? str_replace( 'http://localhost/lote4kids-new/', 'https://lote4kids.com/', $row['pages'][ $i ]['audio_file'] )
                    : '';
                echo '<td>' . esc_html( $audio_file ) . '</td>';
                echo '<td>' . esc_html( $page_number ) . '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No posts found.</p>';
    }

    exit;
} );
*/
?>
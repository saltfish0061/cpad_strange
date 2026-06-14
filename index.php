<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Universal Sambal</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <style>
    :root {
      --green-900: #7a1f16;
      --green-800: #9f2d1f;
      --green-700: #c84425;
      --green-100: #ffe0c7;
      --cream: #fff6ea;
      --yellow: #ffd447;
      --orange: #ef5423;
      --sambal-dark: #43110d;
      --leaf: #2f6f43;
      --ink: #171717;
      --muted: #6d746f;
      --line: #e8e3da;
      --white: #ffffff;
      --shadow: 0 18px 45px rgba(86, 25, 15, 0.16);
      --hero-food-size: 1540px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      color: var(--ink);
      background: var(--cream);
      font-family: 'Playfair Display', Georgia, sans-serif;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    button {
      font: inherit;
    }

    .app-shell {
      min-height: 100vh;
      background: #ffffff;
    }

    .landing-top {
      position: relative;
      min-height: 720px;
      overflow: hidden;
      background:
        radial-gradient(circle at 76% 16%, rgba(255, 212, 71, 0.18), transparent 24%),
        linear-gradient(135deg, var(--sambal-dark), var(--green-900) 48%, var(--green-800));
      color: var(--white);
    }

    .landing-top::before {
      position: absolute;
      inset: 0;
      content: "";
      opacity: 0.075;
      background-image: url("data:image/svg+xml,%3Csvg width='560' height='360' viewBox='0 0 560 360' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M30 78 C78 14 150 20 181 82 C212 145 136 190 79 158 C22 126 -6 113 30 78Z'/%3E%3Cpath d='M90 54 C104 86 95 118 63 141'/%3E%3Cpath d='M248 38 C284 6 337 25 348 76 C358 122 315 165 266 150 C224 137 214 69 248 38Z'/%3E%3Cpath d='M275 62 C305 71 325 93 333 124'/%3E%3Cpath d='M410 40 C452 18 504 35 524 80 C546 130 504 184 454 178 C397 172 367 104 410 40Z'/%3E%3Cpath d='M431 72 C456 96 479 122 497 153'/%3E%3Cpath d='M22 285 C67 220 147 214 189 270 C219 311 190 354 137 350 C90 347 52 322 22 285Z'/%3E%3Cpath d='M56 293 C92 282 125 283 160 303'/%3E%3Cpath d='M244 254 C272 203 343 204 376 254 C403 294 373 342 319 344 C267 346 221 306 244 254Z'/%3E%3Cpath d='M273 271 C305 257 335 260 360 285'/%3E%3Cpath d='M442 252 C476 208 536 224 548 278 C558 323 512 354 469 337 C433 323 414 289 442 252Z'/%3E%3Cpath d='M461 273 C487 267 512 273 532 293'/%3E%3Cpath d='M168 208 C208 158 263 157 302 204'/%3E%3Cpath d='M338 205 C388 151 462 148 520 201'/%3E%3Cpath d='M5 205 C45 183 82 181 118 206'/%3E%3Ccircle cx='218' cy='111' r='13'/%3E%3Ccircle cx='387' cy='306' r='10'/%3E%3Cpath d='M506 18 C523 22 535 33 541 51'/%3E%3Cpath d='M352 23 C367 17 383 16 399 22'/%3E%3C/g%3E%3C/svg%3E");
      background-position: center top;
      background-repeat: repeat;
      background-size: 560px 360px;
    }

    .landing-top::after {
      position: absolute;
      inset: 0;
      content: "";
      opacity: 0.045;
      background-image: url("data:image/svg+xml,%3Csvg width='700' height='430' viewBox='0 0 700 430' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M72 64 C136 -2 228 3 282 71 C337 140 294 238 198 250 C105 262 18 182 72 64Z'/%3E%3Cpath d='M102 111 C150 101 195 107 239 138'/%3E%3Cpath d='M394 45 C462 3 565 34 612 99 C661 167 620 255 535 270 C443 287 342 181 394 45Z'/%3E%3Cpath d='M421 93 C474 105 522 134 570 181'/%3E%3Cpath d='M18 360 C74 287 171 285 225 354'/%3E%3Cpath d='M292 352 C354 278 458 276 528 354'/%3E%3Cpath d='M560 350 C602 312 653 311 690 354'/%3E%3Ccircle cx='344' cy='151' r='16'/%3E%3Ccircle cx='638' cy='321' r='13'/%3E%3Cpath d='M35 32 C63 42 80 60 89 88'/%3E%3Cpath d='M637 32 C664 47 681 67 688 94'/%3E%3C/g%3E%3C/svg%3E");
      background-position: 180px 60px;
      background-repeat: repeat;
      background-size: 700px 430px;
    }

    .landing-top::before {
      opacity: 0.09;
      background-image: url("data:image/svg+xml,%3Csvg width='1440' height='760' viewBox='0 0 1440 760' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M62 118 C115 46 207 50 246 118 C280 177 220 237 146 219 C72 201 29 163 62 118Z'/%3E%3Cpath d='M92 142 C133 124 179 127 219 158'/%3E%3Cpath d='M331 98 C379 42 465 42 510 102 C558 166 517 251 431 254 C343 257 282 157 331 98Z'/%3E%3Cpath d='M371 120 C410 111 452 121 488 150'/%3E%3Cpath d='M838 74 C898 18 1012 33 1058 103 C1106 176 1041 251 950 240 C864 229 782 127 838 74Z'/%3E%3Cpath d='M880 120 C921 98 998 115 1028 159'/%3E%3Cpath d='M1180 116 C1238 56 1338 68 1371 136 C1407 210 1321 266 1244 231 C1175 200 1139 159 1180 116Z'/%3E%3Cpath d='M1220 152 C1268 132 1312 144 1352 181'/%3E%3Cpath d='M96 504 C144 438 238 443 278 512 C309 566 259 633 184 626 C99 618 54 561 96 504Z'/%3E%3Cpath d='M123 531 C166 512 216 516 253 550'/%3E%3Cpath d='M1092 524 C1136 458 1239 460 1281 529 C1319 591 1264 657 1188 645 C1100 632 1050 587 1092 524Z'/%3E%3Cpath d='M1120 548 C1163 525 1226 537 1260 575'/%3E%3Cpath d='M555 586 C613 523 704 524 755 589 C803 651 753 715 669 710 C585 705 505 641 555 586Z'/%3E%3Cpath d='M595 615 C638 592 699 599 732 638'/%3E%3Cpath d='M716 95 C692 141 682 190 706 237 C736 294 812 307 852 263'/%3E%3Cpath d='M734 122 C766 153 792 190 809 238'/%3E%3Cpath d='M376 453 C421 398 489 391 542 438'/%3E%3Cpath d='M394 489 C443 459 502 462 553 493'/%3E%3Cpath d='M889 464 C940 416 1014 411 1068 456'/%3E%3Cpath d='M915 501 C964 471 1025 474 1078 507'/%3E%3Cpath d='M1284 360 C1319 329 1377 340 1398 381'/%3E%3Cpath d='M1267 395 C1306 377 1354 381 1394 410'/%3E%3Cpath d='M46 334 C93 292 163 290 213 333'/%3E%3Cpath d='M61 371 C109 348 168 351 219 382'/%3E%3Cpath d='M627 360 C663 316 730 323 755 376 C783 437 708 493 648 448 C604 415 596 396 627 360Z'/%3E%3Cpath d='M655 384 C684 363 722 372 741 407'/%3E%3Cpath d='M260 708 C288 677 334 679 361 714'/%3E%3Cpath d='M977 701 C1015 672 1064 676 1098 711'/%3E%3Ccircle cx='282' cy='324' r='15'/%3E%3Ccircle cx='580' cy='190' r='12'/%3E%3Ccircle cx='1152' cy='332' r='14'/%3E%3Cpath d='M1315 38 C1342 47 1361 66 1369 96'/%3E%3Cpath d='M47 690 C72 673 102 669 132 679'/%3E%3C/g%3E%3C/svg%3E");
      background-position: center top;
      background-repeat: no-repeat;
      background-size: min(1440px, 132vw) auto;
    }

    .landing-top::after {
      opacity: 0.055;
      background-image: url("data:image/svg+xml,%3Csvg width='1440' height='760' viewBox='0 0 1440 760' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M179 70 C244 10 356 20 414 92 C475 168 427 278 318 291 C202 305 107 158 179 70Z'/%3E%3Cpath d='M219 128 C271 105 341 116 387 164'/%3E%3Cpath d='M1040 84 C1118 24 1238 45 1287 121 C1336 198 1269 296 1166 277 C1062 258 970 139 1040 84Z'/%3E%3Cpath d='M1086 132 C1141 103 1222 123 1260 176'/%3E%3Cpath d='M745 542 C805 473 916 483 969 558 C1016 625 952 710 855 699 C761 688 684 613 745 542Z'/%3E%3Cpath d='M786 589 C839 557 915 568 952 621'/%3E%3Cpath d='M29 444 C82 385 174 390 218 454 C262 518 210 596 125 585 C45 575 -18 500 29 444Z'/%3E%3Cpath d='M64 489 C111 460 174 470 207 513'/%3E%3Cpath d='M570 40 C620 24 679 31 725 61'/%3E%3Cpath d='M544 86 C605 56 693 63 752 112'/%3E%3Cpath d='M1212 468 C1269 426 1346 432 1398 482'/%3E%3Cpath d='M1200 518 C1265 485 1360 493 1424 548'/%3E%3Cpath d='M420 642 C464 608 532 613 575 657'/%3E%3Cpath d='M462 702 C508 672 558 675 604 710'/%3E%3Ccircle cx='669' cy='259' r='18'/%3E%3Ccircle cx='994' cy='386' r='16'/%3E%3Ccircle cx='298' cy='520' r='13'/%3E%3Cpath d='M1375 704 C1390 677 1412 662 1435 657'/%3E%3Cpath d='M815 300 C842 267 894 266 922 304'/%3E%3Cpath d='M835 339 C867 321 901 326 928 352'/%3E%3C/g%3E%3C/svg%3E");
      background-position: center 8px;
      background-repeat: no-repeat;
      background-size: min(1440px, 138vw) auto;
    }

    .landing-top::before {
      opacity: 0.085;
      background-image:
        url("data:image/svg+xml,%3Csvg width='360' height='250' viewBox='0 0 360 250' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M66 190 C32 145 35 92 84 58 C128 28 183 37 220 78 C262 126 247 186 198 211 C154 235 100 231 66 190Z'/%3E%3Cpath d='M116 78 C132 120 162 154 202 179'/%3E%3Cpath d='M72 138 C118 132 162 117 208 85'/%3E%3Cpath d='M238 50 C272 28 310 35 330 62'/%3E%3Cpath d='M252 82 C282 72 312 82 330 108'/%3E%3C/g%3E%3C/svg%3E"),
        url("data:image/svg+xml,%3Csvg width='300' height='210' viewBox='0 0 300 210' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M52 132 C88 64 168 38 232 74 C202 142 118 170 52 132Z'/%3E%3Cpath d='M95 119 C132 104 172 88 218 74'/%3E%3Cpath d='M224 74 C246 45 270 36 286 42'/%3E%3Cpath d='M55 156 C88 184 144 190 192 164'/%3E%3C/g%3E%3C/svg%3E"),
        url("data:image/svg+xml,%3Csvg width='330' height='230' viewBox='0 0 330 230' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M46 126 C86 184 222 190 282 126'/%3E%3Cpath d='M72 124 C90 82 238 82 258 124'/%3E%3Cpath d='M92 120 C102 148 222 148 240 120'/%3E%3Cpath d='M120 70 C108 52 110 36 132 26'/%3E%3Cpath d='M164 70 C152 50 158 34 180 24'/%3E%3Cpath d='M208 70 C196 52 198 38 220 28'/%3E%3C/g%3E%3C/svg%3E");
      background-position: 3% 8%, 86% 13%, 48% 83%;
      background-repeat: no-repeat;
      background-size: 360px 250px, 300px 210px, 330px 230px;
    }

    .landing-top::after {
      opacity: 0.07;
      background-image:
        url("data:image/svg+xml,%3Csvg width='320' height='230' viewBox='0 0 320 230' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M42 178 C78 120 122 88 184 74 C224 66 262 50 286 22'/%3E%3Cpath d='M98 104 C82 78 76 48 88 22'/%3E%3Cpath d='M132 90 C120 62 124 36 146 18'/%3E%3Cpath d='M178 78 C166 52 174 28 196 14'/%3E%3Cpath d='M224 62 C210 38 218 20 242 10'/%3E%3C/g%3E%3C/svg%3E"),
        url("data:image/svg+xml,%3Csvg width='280' height='210' viewBox='0 0 280 210' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='white' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M46 140 C74 86 128 62 184 78 C226 90 248 118 246 154 C206 182 116 182 46 140Z'/%3E%3Cpath d='M76 136 C116 118 170 112 222 132'/%3E%3Cpath d='M116 76 C122 48 144 30 176 24'/%3E%3Cpath d='M156 78 C174 54 198 44 224 48'/%3E%3C/g%3E%3C/svg%3E");
      background-position: 15% 88%, 94% 68%;
      background-repeat: no-repeat;
      background-size: 320px 230px, 280px 210px;
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 20;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
      min-height: 76px;
      padding: 0 6vw;
      background: rgba(122, 31, 22, 0.92);
      border-bottom: 1px solid rgba(255, 255, 255, 0.09);
      backdrop-filter: blur(16px);
    }

    .brand {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-size: 21px;
      font-weight: 800;
      color: var(--white);
      white-space: nowrap;
    }

    .brand-mark {
      display: grid;
      place-items: center;
      width: 38px;
      height: 38px;
      color: var(--green-900);
      background: var(--yellow);
      border-radius: 50%;
      font-weight: 900;
    }

    .nav {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .nav-link,
    .pill-button {
      min-height: 40px;
      padding: 10px 16px;
      border: 1px solid transparent;
      border-radius: 999px;
      background: transparent;
      color: rgba(255, 255, 255, 0.88);
      cursor: pointer;
      font-size: 14px;
      font-weight: 700;
      transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease, color 180ms ease, border-color 180ms ease;
    }

    .nav-link:hover,
    .nav-link.active {
      background: rgba(255, 255, 255, 0.12);
      color: var(--white);
    }

    .nav-link:hover,
    .pill-button:hover {
      transform: translateY(-2px);
    }

    .pill-button:hover {
      box-shadow: 0 10px 20px rgba(67, 17, 13, 0.16);
    }

    .pill-button:active,
    .cta:active,
    .add-button:active {
      transform: translateY(0) scale(0.97);
    }

    .top-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      white-space: nowrap;
    }

    .pill-button {
      border-color: var(--green-900);
      color: var(--green-900);
    }

    .pill-button.primary {
      border-color: var(--green-900);
      background: var(--green-900);
      color: var(--white);
    }

    .topbar .pill-button {
      border-color: rgba(255, 255, 255, 0.78);
      color: var(--white);
    }

    .topbar .pill-button.primary {
      border-color: var(--white);
      background: var(--white);
      color: var(--green-900);
    }

    .page {
      width: min(1180px, 88vw);
      margin: 0 auto;
    }

    .hero {
      display: grid;
      grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
      gap: 42px;
      align-items: center;
      min-height: 640px;
      padding: 64px 0 86px;
    }

    .hero-copy,
    .hero-media {
      position: relative;
      z-index: 1;
    }

    .hero-copy {
      z-index: 3;
    }

    .eyebrow {
      margin: 0 0 14px;
      color: var(--yellow);
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 0;
      text-transform: uppercase;
    }

    .hero h1 {
      max-width: 590px;
      margin: 0;
      font-size: clamp(44px, 6vw, 74px);
      line-height: 0.98;
      letter-spacing: 0;
    }

    .hero-text {
      max-width: 520px;
      margin: 24px 0 32px;
      color: rgba(255, 255, 255, 0.86);
      font-size: 16px;
      line-height: 1.8;
    }

    .hero-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .cta {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 46px;
      padding: 0 22px;
      border: 1px solid transparent;
      border-radius: 999px;
      background: var(--yellow);
      color: #201800;
      cursor: pointer;
      font-weight: 800;
      overflow: hidden;
      transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
    }

    .cta::after {
      position: absolute;
      inset: 0;
      content: "";
      background: linear-gradient(110deg, transparent 0%, rgba(255, 255, 255, 0.44) 45%, transparent 72%);
      transform: translateX(-130%);
      transition: transform 420ms ease;
    }

    .cta:hover {
      transform: translateY(-3px);
      box-shadow: 0 16px 28px rgba(67, 17, 13, 0.18);
    }

    .cta:hover::after {
      transform: translateX(130%);
    }

    .cta.secondary {
      border-color: rgba(255, 255, 255, 0.55);
      background: transparent;
      color: var(--white);
    }

    .hero-media {
      min-height: 430px;
      display: grid;
      place-items: center;
      pointer-events: none;
    }

    .food-stage {
      position: relative;
      width: min(var(--hero-food-size), 150%);
      min-height: 470px;
      display: grid;
      place-items: center;
    }

    .food-stage::before {
      position: absolute;
      left: 50%;
      bottom: 34px;
      width: 72%;
      height: 30px;
      content: "";
      background: rgba(0, 0, 0, 0.22);
      border-radius: 50%;
      filter: blur(18px);
      transform: translateX(-50%);
    }

    .food-backdrop {
      position: absolute;
      width: 82%;
      aspect-ratio: 1;
      background: rgba(255, 212, 71, 0.12);
      border: 2px solid rgba(255, 255, 255, 0.18);
      border-radius: 42% 58% 63% 37% / 40% 44% 56% 60%;
      clip-path: polygon(8% 26%, 23% 10%, 45% 15%, 61% 3%, 83% 18%, 91% 39%, 82% 59%, 94% 80%, 70% 92%, 48% 84%, 30% 96%, 13% 78%, 18% 57%, 5% 43%);
      transform: rotate(-7deg);
    }

    .food-backdrop::before,
    .food-backdrop::after {
      position: absolute;
      inset: 8%;
      content: "";
      border: 2px solid rgba(255, 255, 255, 0.16);
      border-radius: 48% 52% 38% 62% / 48% 41% 59% 52%;
      clip-path: polygon(9% 31%, 28% 8%, 47% 18%, 66% 9%, 88% 30%, 80% 51%, 93% 71%, 69% 89%, 46% 78%, 27% 92%, 10% 72%, 19% 54%);
    }

    .food-backdrop::after {
      inset: 18%;
      border-color: rgba(255, 212, 71, 0.2);
      transform: rotate(14deg);
    }

    .food-backdrop {
      width: 128%;
      height: 122%;
      aspect-ratio: auto;
      background: transparent;
      background-image: url("data:image/svg+xml,%3Csvg width='980' height='720' viewBox='0 0 980 720' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='140,510 330,130 505,580 710,125 820,455' fill='none' stroke='%23ffd447' stroke-width='230' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpolyline points='150,502 325,145 505,568 720,125 798,436' fill='none' stroke='white' stroke-opacity='.2' stroke-width='116' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-position: center;
      background-repeat: no-repeat;
      background-size: contain;
      border: 0;
      border-radius: 0;
      clip-path: none;
      pointer-events: none;
      transform: rotate(39deg) translateY(10px);
    }

    .food-backdrop::before,
    .food-backdrop::after {
      inset: 0;
      border: 0;
      border-radius: 0;
      clip-path: none;
      background-image: url("data:image/svg+xml,%3Csvg width='980' height='720' viewBox='0 0 980 720' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='160,488 315,205 498,520 705,160 835,442' fill='none' stroke='%23fff3c0' stroke-opacity='.24' stroke-width='42' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-position: center;
      background-repeat: no-repeat;
      background-size: contain;
      transform: rotate(4deg);
    }

    .food-backdrop::after {
      opacity: 0.55;
      transform: rotate(-6deg) translateY(16px);
    }

    .hero-food {
      position: relative;
      z-index: 2;
      width: min(var(--hero-food-size), 150%);
      max-height: 450px;
      object-fit: contain;
      filter: drop-shadow(0 28px 34px rgba(0, 0, 0, 0.64));
      transform: translateY(-30px) scale(1.35);
    }

    .hero-food.has-background {
      width: min(390px, 88%);
      aspect-ratio: 1;
      object-fit: cover;
      border: 12px solid rgba(255, 255, 255, 0.16);
      border-radius: 50%;
    }

    .rating-card,
    .quick-card {
      position: absolute;
      z-index: 2;
      width: 190px;
      padding: 16px;
      background: rgba(255, 255, 255, 0.96);
      border-radius: 8px;
      color: var(--ink);
      box-shadow: 0 18px 40px rgba(67, 17, 13, 0.18);
    }

    .rating-card {
      right: 38px;
      bottom: 54px;
    }

    .quick-card {
      right: 74px;
      top: 78px;
    }

    .mini-label {
      display: block;
      margin-bottom: 6px;
      color: var(--muted);
      font-size: 12px;
      font-weight: 700;
    }

    .stars {
      color: #f8b600;
      font-size: 14px;
    }

    .section {
      padding: 58px 0;
    }

    .promo-section {
      background: var(--white);
    }

    .section-head {
      display: flex;
      align-items: end;
      justify-content: space-between;
      gap: 24px;
      margin-bottom: 24px;
      z-index: 4;
    }

    .section-head h2 {
      margin: 0;
      font-size: clamp(28px, 4vw, 42px);
      letter-spacing: 0;
    }

    .section-head p {
      max-width: 430px;
      margin: 0;
      color: var(--muted);
      line-height: 1.7;
    }

    .promo-grid {
      display: grid;
      grid-template-columns: 1fr 1.05fr;
      gap: 18px;
    }

    .promo-stack {
      display: grid;
      gap: 18px;
    }

    .promo {
      min-height: 210px;
      display: grid;
      grid-template-columns: 1.05fr 0.95fr;
      gap: 18px;
      align-items: center;
      padding: 26px;
      overflow: hidden;
      border-radius: 30px;
      background: #ffe58f;
      transition: transform 220ms ease, box-shadow 220ms ease;
    }

    .promo:hover {
      transform: translateY(-6px);
      box-shadow: 0 18px 34px rgba(67, 17, 13, 0.16);
    }

    .promo.green {
      background: var(--sambal-dark);
      color: var(--white);
    }

    .promo.red {
      position: relative;
      overflow: visible;
      min-height: 438px;
      background: #d93b22;
      color: var(--white);
      overflow: hidden;
    }

    .promo.red>div {
      position: relative;
      z-index: 2;
    }

    .promo h3 {
      margin: 0 0 10px;
      font-size: 28px;
      line-height: 1.15;
    }

    .promo p {
      margin: 0 0 18px;
      color: rgba(23, 23, 23, 0.72);
      line-height: 1.5;
    }

    .promo.red p {
      color: rgba(255, 255, 255, 0.82);
    }

    .promo.green p {
      color: rgba(255, 255, 255, 0.82);
    }

    .promo img {
      width: 100%;
      height: 170px;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 14px 30px rgba(0, 0, 0, 0.14);
    }

    .promo.red img {
      height: 310px;
      object-fit: contain;
      background: rgba(255, 255, 255, 0.08);
    }

    .promo img.cutout {
      position: relative;
      z-index: 1;
      width: 100%;
      height: 260px;
      object-fit: contain;
      background: transparent;
      border-radius: 0;
      box-shadow: none;
      filter: drop-shadow(0 24px 24px rgba(67, 17, 13, 0.58));
      transform: none;
    }

    .promo-visual-rider {
      width: 120px;
      height: 120px;
      transform: translateX(40px) scale(2.5);
      transform-origin: center;
    }

    .promo img.cutout.promo-img-menu {
      width: 118%;
      height: 260px;
      transform: translateX(10%) translateY(8%) scale(1.65);
    }

    .promo img.cutout.promo-img-splash {
      width: 118%;
      height: 360px;
      transform: translateX(8%) translateY(5%) scale(2.18);
    }

    .cart-button {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      /* Smooth spacing between the icon and the text */
    }

    .cart-icon {
      width: 18px;
      /* Matches standard text heights */
      height: 18px;
      display: inline-block;
      vertical-align: middle;
      transition: transform 180ms ease;
    }

    .cart-button:hover .cart-icon {
      transform: translateX(2px) rotate(-7deg);
    }


    .category-band {
      background: var(--green-100);
    }

    .category-layout {
      display: grid;
      grid-template-columns: 0.8fr 1.2fr;
      gap: 40px;
      align-items: center;
    }

    .category-art {
      position: relative;
      min-height: 420px;
      display: grid;
      place-items: center;
    }

    .category-art::before {
      position: absolute;
      width: min(540px, 82%);
      aspect-ratio: 1.08;
      content: "";
      background: linear-gradient(135deg, rgba(255, 212, 71, 0.65), rgba(217, 59, 34, 0.22));
      border-radius: 34% 66% 35% 75% / 54% 36% 64% 46%;
      transform: translate(24px, 24px) rotate(-5deg);
      filter: blur(0.2px);
      opacity: 0.9;
    }

    .category-art img {
      position: relative;
      z-index: 2;
      width: 100%;
      height: 260px;
      object-fit: contain;
      background: transparent;
      border-radius: 0;
      box-shadow: none;
      filter: drop-shadow(0 24px 24px rgba(67, 17, 13, 0.58));
      transform: scale(1.8);
    }

    .float-tile {
      position: absolute;
      z-index: 3;
      width: 150px;
      padding: 18px 12px;
      text-align: center;
      background: rgba(255, 255, 255, 0.96);
      border-radius: 8px;
      box-shadow: 0 18px 35px rgba(122, 31, 22, 0.15);
      transition: transform 220ms ease, box-shadow 220ms ease;
    }

    .float-tile:hover {
      transform: translateY(-6px) rotate(-1deg);
      box-shadow: 0 22px 38px rgba(122, 31, 22, 0.18);
    }

    .float-tile strong {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .float-tile span {
      color: var(--muted);
      font-size: 12px;
    }

    .tile-food {
      left: 0;
      top: 76px;
    }

    .tile-drink {
      right: 0;
      top: 34px;
    }

    .tile-cart {
      left: 80px;
      bottom: 32px;
    }

    .tile-order {
      right: 64px;
      bottom: 70px;
    }

    .card-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .food-card,
    .empty-panel {
      background: var(--white);
      border: 1px solid var(--line);
      border-radius: 30px;
      box-shadow: 0 12px 30px rgba(42, 48, 43, 0.08);
    }

    .food-card {
      overflow: hidden;
      transition: transform 220ms ease, box-shadow 220ms ease;
    }

    .food-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 18px 34px rgba(67, 17, 13, 0.14);
    }

    .food-card img {
      width: 100%;
      height: 190px;
      object-fit: cover;
      background: radial-gradient(circle at 76% 16%, rgba(255, 212, 71, 0.28), transparent 24%),
        linear-gradient(215deg, var(--sambal-dark) 40%, var(--green-900) 68%, var(--green-700));
    }

    .food-card-body {
      padding: 18px;
    }

    .food-card h3 {
      margin: 0 0 8px;
      font-size: 19px;
    }

    .meta {
      margin: 0 0 18px;
      color: var(--muted);
      font-size: 14px;
    }

    .price-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      font-weight: 800;
    }

    .add-button {
      display: grid;
      place-items: center;
      width: 36px;
      height: 36px;
      border: 0;
      border-radius: 50%;
      background: var(--green-800);
      color: var(--white);
      cursor: pointer;
      font-size: 22px;
      line-height: 1;
      transition: transform 170ms ease, box-shadow 170ms ease, background-color 170ms ease;
    }

    .add-button:hover {
      background: var(--green-900);
      transform: scale(1.08);
      box-shadow: 0 10px 18px rgba(67, 17, 13, 0.22);
    }

    .route-panel {
      padding: 58px 0 80px;
    }

    .empty-panel {
      min-height: 420px;
      display: grid;
      place-items: center;
      padding: 42px;
      text-align: center;
    }

    .empty-panel h1 {
      margin: 0 0 12px;
      font-size: clamp(34px, 5vw, 54px);
    }

    .empty-panel p {
      max-width: 560px;
      margin: 0 auto 24px;
      color: var(--muted);
      line-height: 1.7;
    }

    .link-list {
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .vendor-page {
      padding: 42px 0 70px;
    }

    .vendor-header {
      display: flex;
      align-items: end;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 20px;
    }

    .vendor-header h1 {
      margin: 0;
      font-size: clamp(34px, 5vw, 56px);
      line-height: 1;
    }

    .vendor-header p {
      margin: 10px 0 0;
      color: var(--muted);
      line-height: 1.6;
    }

    .vendor-tabs {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin: 24px 0;
    }

    .vendor-tab,
    .small-action,
    .danger-action {
      min-height: 38px;
      padding: 9px 14px;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: var(--white);
      color: var(--ink);
      cursor: pointer;
      font-weight: 800;
      transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
    }

    .vendor-tab:hover,
    .small-action:hover,
    .danger-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 18px rgba(67, 17, 13, 0.12);
    }

    .vendor-tab.active,
    .small-action.primary {
      border-color: var(--green-900);
      background: var(--green-900);
      color: var(--white);
    }

    .danger-action {
      border-color: #f0b6aa;
      color: #9f2d1f;
    }

    .vendor-alert {
      margin: 0 0 16px;
      padding: 12px 14px;
      border: 1px solid #f3c05c;
      border-radius: 8px;
      background: #fff3c0;
      color: #5a3004;
      font-weight: 700;
    }

    .vendor-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
    }

    .metric-card,
    .vendor-panel {
      border: 1px solid var(--line);
      border-radius: 8px;
      background: var(--white);
      box-shadow: 0 12px 28px rgba(42, 48, 43, 0.08);
    }

    .metric-card {
      padding: 18px;
    }

    .metric-card span {
      display: block;
      color: var(--muted);
      font-size: 13px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .metric-card strong {
      display: block;
      margin-top: 10px;
      font-size: 30px;
    }

    .vendor-panel {
      margin-top: 16px;
      overflow: hidden;
    }

    .vendor-panel-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid var(--line);
    }

    .vendor-panel-head h2 {
      margin: 0;
      font-size: 22px;
    }

    .vendor-table {
      width: 100%;
      border-collapse: collapse;
    }

    .vendor-table th,
    .vendor-table td {
      padding: 13px 14px;
      border-bottom: 1px solid var(--line);
      text-align: left;
      vertical-align: middle;
    }

    .vendor-table th {
      color: var(--muted);
      font-size: 12px;
      text-transform: uppercase;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      min-height: 26px;
      padding: 4px 10px;
      border-radius: 999px;
      background: #fff3c0;
      color: #5a3004;
      font-size: 12px;
      font-weight: 900;
      text-transform: capitalize;
    }

    .status-pill.completed,
    .status-pill.available {
      background: #dff3df;
      color: #21582f;
    }

    .status-pill.cancelled,
    .status-pill.sold-out {
      background: #ffe0d8;
      color: #9f2d1f;
    }

    .status-pill.ready {
      background: #dcecff;
      color: #164b86;
    }

    .inline-actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      align-items: center;
    }

    .vendor-select,
    .vendor-input,
    .vendor-textarea {
      width: 100%;
      min-height: 40px;
      padding: 9px 11px;
      border: 1px solid var(--line);
      border-radius: 8px;
      background: var(--white);
      color: var(--ink);
      font: inherit;
    }

    .vendor-textarea {
      min-height: 84px;
      resize: vertical;
    }

    .vendor-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      padding: 18px;
      border-bottom: 1px solid var(--line);
      background: #fffaf2;
    }

    .vendor-form label {
      display: grid;
      gap: 6px;
      color: var(--muted);
      font-size: 13px;
      font-weight: 800;
    }

    .vendor-form .wide {
      grid-column: 1 / -1;
    }

    .order-detail {
      padding: 18px;
      background: #fffaf2;
      border-bottom: 1px solid var(--line);
    }

    .order-detail h3 {
      margin: 0 0 10px;
    }

    .order-detail ul {
      margin: 10px 0 0;
      padding-left: 18px;
      line-height: 1.8;
    }

    .muted-text {
      color: var(--muted);
    }

    .footer {
      padding: 30px 6vw;
      color: rgba(255, 255, 255, 0.8);
      background: var(--green-900);
      font-size: 14px;
      text-align: center;
    }

    @media (max-width: 980px) {
      .topbar {
        align-items: flex-start;
        flex-direction: column;
        padding-top: 16px;
        padding-bottom: 16px;
      }

      .top-actions {
        width: 100%;
        justify-content: space-between;
      }

      .hero,
      .category-layout {
        grid-template-columns: 1fr;
      }

      .hero {
        padding: 42px 28px;
      }

      .rating-card,
      .quick-card {
        position: static;
        width: 100%;
        margin-top: 14px;
      }

      .promo-grid,
      .card-grid,
      .vendor-grid {
        grid-template-columns: 1fr;
      }

      .vendor-header {
        align-items: flex-start;
        flex-direction: column;
      }

      .vendor-table {
        display: block;
        overflow-x: auto;
      }

      .promo.red {
        min-height: 210px;
      }
    }

    @media (max-width: 640px) {
      .page {
        width: min(100% - 28px, 1180px);
      }

      .nav {
        justify-content: flex-start;
      }

      .nav-link,
      .pill-button {
        padding: 9px 12px;
        font-size: 13px;
      }

      .hero h1 {
        font-size: 44px;
      }

      .promo {
        grid-template-columns: 1fr;
      }

      .category-art {
        min-height: 560px;
      }

      .vendor-form {
        grid-template-columns: 1fr;
      }

      .tile-food,
      .tile-drink,
      .tile-cart,
      .tile-order {
        left: auto;
        right: auto;
        top: auto;
        bottom: auto;
      }

      .tile-food {
        top: 18px;
        left: 8px;
      }

      .tile-drink {
        top: 92px;
        right: 8px;
      }

      .tile-cart {
        bottom: 98px;
        left: 14px;
      }

      .tile-order {
        bottom: 22px;
        right: 14px;
      }
    }

    @media (prefers-reduced-motion: reduce) {

      *,
      *::before,
      *::after {
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
      }
    }
  </style>
</head>

<body>
  <div id="app"></div>

  <script>
    const { createApp, computed, onMounted, onUnmounted, ref } = Vue;

    const routes = {
      home: {
        label: 'Home',
        title: 'Universal Sambal',
        note: 'Welcome page and main discovery area.'
      },
      menu: {
        label: 'Menu',
        title: 'Menu',
        note: 'Empty menu page. Paste the menu API or page link here later.'
      },
      cart: {
        label: 'Cart',
        title: 'Cart',
        note: 'Empty cart page. This will hold selected food and drink items.'
      },
      orders: {
        label: 'Orders',
        title: 'Orders',
        note: 'Empty order tracking and history page.'
      },
      profile: {
        label: 'Profile',
        title: 'Profile',
        note: 'Empty customer profile page.'
      },
      vendor: {
        label: 'Vendor',
        title: 'Vendor Dashboard',
        note: 'Empty admin/vendor area for menu management, incoming orders, and sales records.'
      },
      login: {
        label: 'Login',
        title: 'Login',
        note: 'Empty authentication page. Connect this to your login/register module later.'
      }
    };

    createApp({
      setup() {
        const route = ref('home');
        const cartCount = ref(0);
        const apiBase = 'api';
        const vendorTab = ref('dashboard');
        const vendorLoading = ref(false);
        const vendorMessage = ref('');
        const vendorMenu = ref([]);
        const vendorOrders = ref([]);
        const vendorSales = ref({
          summary: { completed_orders: 0, total_sales: 0 },
          popular_items: [],
          status_counts: []
        });
        const selectedOrder = ref(null);
        const selectedOrderItems = ref([]);
        const editingItemId = ref('');
        const menuForm = ref({
          name: '',
          description: '',
          price: '',
          category: 'food'
        });

        const updateRoute = () => {
          const nextRoute = window.location.hash.replace('#/', '') || 'home';
          route.value = routes[nextRoute] ? nextRoute : 'home';
          if (route.value === 'vendor') {
            loadVendorData();
          }
        };

        onMounted(() => {
          updateRoute();
          window.addEventListener('hashchange', updateRoute);
        });

        onUnmounted(() => {
          window.removeEventListener('hashchange', updateRoute);
        });

        const currentRoute = computed(() => routes[route.value]);
        const navItems = computed(() => ['home', 'menu', 'orders', 'profile', 'vendor']);

        const previewItems = [
          {
            name: 'Ayam Geprek Sambal Merah',
            category: 'Food',
            price: 'RM 11.20',
            image: 'images/top picks ayam merah.png'
          },
          {
            name: 'Ayam Geprek Sambal Hijau',
            category: 'Food',
            price: 'RM 11.20',
            image: 'images/top picks ayam hijau.png'
          },
          {
            name: 'Jus Tembikai Susu',
            category: 'Drink',
            price: 'RM 6.40',
            image: 'images/tembikai susu.png'
          }
        ];

        const addPreviewItem = () => {
          cartCount.value += 1;
        };

        const formatMoney = (value) => {
          return `RM ${Number(value || 0).toFixed(2)}`;
        };

        const setVendorMessage = (message) => {
          vendorMessage.value = message;
          if (message) {
            window.setTimeout(() => {
              if (vendorMessage.value === message) {
                vendorMessage.value = '';
              }
            }, 3600);
          }
        };

        const apiRequest = async (path, options = {}) => {
          const response = await fetch(`${apiBase}${path}`, {
            headers: {
              'Content-Type': 'application/json',
              ...(options.headers || {})
            },
            ...options
          });
          const data = await response.json().catch(() => ({}));

          if (!response.ok) {
            const message = data.errors ? data.errors.join(' ') : (data.message || 'Request failed.');
            throw new Error(message);
          }

          return data;
        };

        const loadVendorMenu = async () => {
          const data = await apiRequest('/vendor/menu');
          vendorMenu.value = data.items || [];
        };

        const loadVendorOrders = async () => {
          const data = await apiRequest('/vendor/orders');
          vendorOrders.value = data.orders || [];
        };

        const loadVendorSales = async () => {
          vendorSales.value = await apiRequest('/vendor/sales');
        };

        const loadVendorData = async () => {
          vendorLoading.value = true;
          try {
            await Promise.all([
              loadVendorMenu(),
              loadVendorOrders(),
              loadVendorSales()
            ]);
          } catch (error) {
            setVendorMessage(error.message || 'Could not load vendor data.');
          } finally {
            vendorLoading.value = false;
          }
        };

        const resetMenuForm = () => {
          editingItemId.value = '';
          menuForm.value = {
            name: '',
            description: '',
            price: '',
            category: 'food'
          };
        };

        const editMenuItem = (item) => {
          editingItemId.value = item.item_id;
          menuForm.value = {
            name: item.name,
            description: item.description || '',
            price: item.price,
            category: item.category
          };
          vendorTab.value = 'menu';
        };

        const saveMenuItem = async () => {
          const payload = {
            ...menuForm.value,
            price: Number(menuForm.value.price)
          };

          try {
            if (editingItemId.value) {
              await apiRequest(`/vendor/menu/${editingItemId.value}`, {
                method: 'PUT',
                body: JSON.stringify(payload)
              });
              setVendorMessage('Menu item updated.');
            } else {
              await apiRequest('/vendor/menu', {
                method: 'POST',
                body: JSON.stringify(payload)
              });
              setVendorMessage('Menu item added.');
            }

            resetMenuForm();
            await Promise.all([loadVendorMenu(), loadVendorSales()]);
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const toggleAvailability = async (item) => {
          try {
            await apiRequest(`/vendor/menu/${item.item_id}/availability`, {
              method: 'PATCH',
              body: JSON.stringify({ is_available: !Number(item.is_available) })
            });
            await loadVendorMenu();
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const deleteMenuItem = async (item) => {
          if (!window.confirm(`Delete ${item.name}? Items used in orders cannot be deleted.`)) {
            return;
          }

          try {
            await apiRequest(`/vendor/menu/${item.item_id}`, { method: 'DELETE' });
            setVendorMessage('Menu item deleted.');
            await loadVendorMenu();
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const viewOrder = async (order) => {
          try {
            const data = await apiRequest(`/vendor/orders/${order.order_id}`);
            selectedOrder.value = data.order;
            selectedOrderItems.value = data.items || [];
            vendorTab.value = 'orders';
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const updateOrderStatus = async (order, status) => {
          try {
            await apiRequest(`/vendor/orders/${order.order_id}/status`, {
              method: 'PATCH',
              body: JSON.stringify({ status })
            });
            setVendorMessage('Order status updated.');
            await Promise.all([loadVendorOrders(), loadVendorSales()]);
            if (selectedOrder.value && selectedOrder.value.order_id === order.order_id) {
              selectedOrder.value.status = status;
            }
          } catch (error) {
            setVendorMessage(error.message);
          }
        };

        const pendingOrders = computed(() => vendorOrders.value.filter((order) => order.status !== 'completed' && order.status !== 'cancelled'));
        const availableCount = computed(() => vendorMenu.value.filter((item) => Number(item.is_available)).length);

        return {
          addPreviewItem,
          availableCount,
          cartCount,
          currentRoute,
          deleteMenuItem,
          editMenuItem,
          editingItemId,
          formatMoney,
          loadVendorData,
          menuForm,
          navItems,
          pendingOrders,
          previewItems,
          resetMenuForm,
          route,
          routes,
          saveMenuItem,
          selectedOrder,
          selectedOrderItems,
          toggleAvailability,
          updateOrderStatus,
          vendorLoading,
          vendorMenu,
          vendorMessage,
          vendorOrders,
          vendorSales,
          vendorTab,
          viewOrder
        };
      },
      template: `
        <main class="app-shell">
          <header class="topbar">
            <a class="brand" href="#/home" aria-label="Universal Sambal home">
              <span class="brand-mark">US</span>
              <span>Universal Sambal</span>
            </a>

            <nav class="nav" aria-label="Main navigation">
              <a
                v-for="item in navItems"
                :key="item"
                class="nav-link"
                :class="{ active: route === item }"
                :href="'#/' + item"
              >
                {{ routes[item].label }}
              </a>
            </nav>

            <div class="top-actions">
              <a class="pill-button cart-button" href="#/cart">
                <svg 
                  xmlns="http://w3.org" 
                  viewBox="0 0 24 24" 
                  fill="none" 
                  stroke="currentColor" 
                  stroke-width="2" 
                  stroke-linecap="round" 
                  stroke-linejoin="round" 
                  class="cart-icon"
                >
                  <circle cx="9" cy="21" r="1"></circle>
                  <circle cx="20" cy="21" r="1"></circle>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span>{{ cartCount }}</span>
              </a>
              <a class="pill-button primary" href="#/login">Login</a>
            </div>
          </header>

          <template v-if="route === 'home'">
            <section class="landing-top">
              <div class="page hero">
                <div class="hero-copy">
                  <p class="eyebrow">Online food and drink ordering</p>
                  <h1>Want to get constipated? Try ours</h1>
                  <p class="hero-text">
                    A base customer interface for Universal Sambal. Browse the menu, add items to cart,
                    track orders, and leave the real links ready for the backend module.
                  </p>
                  <div class="hero-actions">
                    <a class="cta" href="#/menu">Explore Menu</a>
                  </div>
                </div>

                <div class="hero-media">
                  <div class="food-stage">
                    <div class="food-backdrop"></div>
                    <img class="hero-food" src="images/test.png" alt="Ayam geprek sambal merah">
                  </div>
                </div>
              </div>
            </section>

            <section class="promo-section">
              <div class="page section">
                <div class="promo-grid">
                  <div class="promo-stack">
                    <article class="promo">
                      <div>
                        <p class="eyebrow">Made fresh</p>
                        <h3>Order ahead during peak hours</h3>
                        <p>Placeholder campaign panel for future offers or announcements.</p>
                        <a class="cta" href="#/orders">Track Order</a>
                      </div>
                      <lottie-player
                        class="promo-visual-rider"
                        src="animations/rider.json"
                        background="transparent"
                        speed="1"
                        loop
                        autoplay>
                      </lottie-player>
                    </article>

                    <article class="promo green">
                      <div>
                        <p class="eyebrow">Customer menu</p>
                        <h3>Browse food and drinks</h3>
                        <p>Empty menu route ready for your menu module link.</p>
                        <a class="cta" href="#/menu">View Menu</a>
                      </div>
                      <img class="cutout promo-img-menu" src="images/separate.png" alt="Ayam geprek sambal hijau">
                    </article>
                  </div>

                  <article class="promo red">
                    <div>
                      <p class="eyebrow">Juices for you</p>
                      <h3>Incoming orders and menu status</h3>
                      <p>Empty dashboard link for admin features when the Slim API is ready.</p>
                      <a class="cta" href="#/menu">I want it</a>
                    </div>
                    <img class="cutout promo-img-splash" src="images/splash.png" alt="Fruit drinks splash">
                  </article>
                </div>
              </div>
            </section>

            <section class="category-band">
              <div class="page section category-layout">
                <div class="section-head">
                  <div>
                    <p class="eyebrow">System modules</p>
                    <h2>Base navigation for customer and vendor flow</h2>
                  </div>
                </div>

                <div class="category-art">
                  <img src="images/top view.png" alt="Rice meal">
                  <div class="float-tile tile-food">
                    <strong>Menu</strong>
                    <span>Browse food and drinks</span>
                  </div>
                  <div class="float-tile tile-drink">
                    <strong>Cart</strong>
                    <span>Adjust quantities</span>
                  </div>
                  <div class="float-tile tile-cart">
                    <strong>Orders</strong>
                    <span>Status and history</span>
                  </div>
                  <div class="float-tile tile-order">
                    <strong>Vendor</strong>
                    <span>Manage records</span>
                  </div>
                </div>
              </div>
            </section>

            <section class="page section">
              <div class="section-head">
                <div>
                  <p class="eyebrow">Preview cards</p>
                  <h2>Top picks shell</h2>
                </div>
                <p>These are only sample UI cards. Later they can be loaded from Slim using the menus table.</p>
              </div>

              <div class="card-grid">
                <article class="food-card" v-for="item in previewItems" :key="item.name">
                  <img :src="item.image" :alt="item.name">
                  <div class="food-card-body">
                    <h3>{{ item.name }}</h3>
                    <p class="meta">{{ item.category }} preview item</p>
                    <div class="price-row">
                      <span>{{ item.price }}</span>
                      <button class="add-button" type="button" @click="addPreviewItem" aria-label="Add preview item">+</button>
                    </div>
                  </div>
                </article>
              </div>
            </section>
          </template>

          <section v-else-if="route === 'vendor'" class="page vendor-page">
            <div class="vendor-header">
              <div>
                <p class="eyebrow">Vendor module</p>
                <h1>Vendor Dashboard</h1>
                <p>Manage menu availability, update incoming orders, and review completed sales records.</p>
              </div>
              <button class="pill-button primary" type="button" @click="loadVendorData">
                {{ vendorLoading ? 'Refreshing...' : 'Refresh Data' }}
              </button>
            </div>

            <p v-if="vendorMessage" class="vendor-alert">{{ vendorMessage }}</p>

            <div class="vendor-tabs" role="tablist" aria-label="Vendor sections">
              <button class="vendor-tab" :class="{ active: vendorTab === 'dashboard' }" type="button" @click="vendorTab = 'dashboard'">Dashboard</button>
              <button class="vendor-tab" :class="{ active: vendorTab === 'orders' }" type="button" @click="vendorTab = 'orders'">Orders</button>
              <button class="vendor-tab" :class="{ active: vendorTab === 'menu' }" type="button" @click="vendorTab = 'menu'">Menu Items</button>
              <button class="vendor-tab" :class="{ active: vendorTab === 'sales' }" type="button" @click="vendorTab = 'sales'">Sales Records</button>
            </div>

            <template v-if="vendorTab === 'dashboard'">
              <div class="vendor-grid">
                <article class="metric-card">
                  <span>Open orders</span>
                  <strong>{{ pendingOrders.length }}</strong>
                </article>
                <article class="metric-card">
                  <span>Completed orders</span>
                  <strong>{{ vendorSales.summary.completed_orders || 0 }}</strong>
                </article>
                <article class="metric-card">
                  <span>Total sales</span>
                  <strong>{{ formatMoney(vendorSales.summary.total_sales) }}</strong>
                </article>
                <article class="metric-card">
                  <span>Available items</span>
                  <strong>{{ availableCount }}</strong>
                </article>
              </div>

              <div class="vendor-panel">
                <div class="vendor-panel-head">
                  <h2>Incoming Orders</h2>
                  <button class="small-action" type="button" @click="vendorTab = 'orders'">View All</button>
                </div>
                <table class="vendor-table">
                  <thead>
                    <tr>
                      <th>Order</th>
                      <th>Customer</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in pendingOrders.slice(0, 6)" :key="order.order_id">
                      <td>{{ order.order_id }}</td>
                      <td>{{ order.customer_name }}</td>
                      <td>{{ formatMoney(order.total_amount) }}</td>
                      <td><span class="status-pill" :class="order.status">{{ order.status }}</span></td>
                      <td><button class="small-action" type="button" @click="viewOrder(order)">Details</button></td>
                    </tr>
                    <tr v-if="pendingOrders.length === 0">
                      <td colspan="5" class="muted-text">No active orders right now.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </template>

            <template v-if="vendorTab === 'orders'">
              <div v-if="selectedOrder" class="order-detail">
                <h3>Order {{ selectedOrder.order_id }} · {{ selectedOrder.customer_name }}</h3>
                <p class="muted-text">{{ selectedOrder.customer_phone }} · {{ formatMoney(selectedOrder.total_amount) }}</p>
                <ul>
                  <li v-for="item in selectedOrderItems" :key="item.order_item_id">
                    {{ item.quantity }}x {{ item.name }} - {{ formatMoney(item.subtotal) }}
                  </li>
                </ul>
              </div>

              <div class="vendor-panel">
                <div class="vendor-panel-head">
                  <h2>Order Dashboard</h2>
                  <span class="muted-text">{{ vendorOrders.length }} orders</span>
                </div>
                <table class="vendor-table">
                  <thead>
                    <tr>
                      <th>Order</th>
                      <th>Customer</th>
                      <th>Phone</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Update</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in vendorOrders" :key="order.order_id">
                      <td><button class="small-action" type="button" @click="viewOrder(order)">{{ order.order_id }}</button></td>
                      <td>{{ order.customer_name }}</td>
                      <td>{{ order.customer_phone }}</td>
                      <td>{{ order.item_count }}</td>
                      <td>{{ formatMoney(order.total_amount) }}</td>
                      <td><span class="status-pill" :class="order.status">{{ order.status }}</span></td>
                      <td>
                        <select class="vendor-select" :value="order.status" @change="updateOrderStatus(order, $event.target.value)">
                          <option value="pending">pending</option>
                          <option value="preparing">preparing</option>
                          <option value="ready">ready</option>
                          <option value="completed">completed</option>
                          <option value="cancelled">cancelled</option>
                        </select>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </template>

            <template v-if="vendorTab === 'menu'">
              <div class="vendor-panel">
                <div class="vendor-panel-head">
                  <h2>{{ editingItemId ? 'Update Menu Item' : 'Add Menu Item' }}</h2>
                  <button v-if="editingItemId" class="small-action" type="button" @click="resetMenuForm">Cancel Edit</button>
                </div>
                <form class="vendor-form" @submit.prevent="saveMenuItem">
                  <label>
                    Name
                    <input class="vendor-input" v-model="menuForm.name" required>
                  </label>
                  <label>
                    Price
                    <input class="vendor-input" v-model="menuForm.price" type="number" min="0.01" step="0.01" required>
                  </label>
                  <label>
                    Category
                    <select class="vendor-select" v-model="menuForm.category" required>
                      <option value="food">food</option>
                      <option value="drink">drink</option>
                    </select>
                  </label>
                  <label class="wide">
                    Description
                    <textarea class="vendor-textarea" v-model="menuForm.description"></textarea>
                  </label>
                  <div class="inline-actions wide">
                    <button class="small-action primary" type="submit">{{ editingItemId ? 'Save Changes' : 'Add Item' }}</button>
                    <button class="small-action" type="button" @click="resetMenuForm">Clear</button>
                  </div>
                </form>

                <table class="vendor-table">
                  <thead>
                    <tr>
                      <th>Item</th>
                      <th>Category</th>
                      <th>Price</th>
                      <th>Availability</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in vendorMenu" :key="item.item_id">
                      <td>
                        <strong>{{ item.name }}</strong>
                        <div class="muted-text">{{ item.item_id }}</div>
                      </td>
                      <td>{{ item.category }}</td>
                      <td>{{ formatMoney(item.price) }}</td>
                      <td>
                        <span class="status-pill" :class="Number(item.is_available) ? 'available' : 'sold-out'">
                          {{ Number(item.is_available) ? 'available' : 'sold out' }}
                        </span>
                      </td>
                      <td>
                        <div class="inline-actions">
                          <button class="small-action" type="button" @click="editMenuItem(item)">Edit</button>
                          <button class="small-action" type="button" @click="toggleAvailability(item)">
                            {{ Number(item.is_available) ? 'Sold Out' : 'Available' }}
                          </button>
                          <button class="danger-action" type="button" @click="deleteMenuItem(item)">Delete</button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </template>

            <template v-if="vendorTab === 'sales'">
              <div class="vendor-grid">
                <article class="metric-card">
                  <span>Completed orders</span>
                  <strong>{{ vendorSales.summary.completed_orders || 0 }}</strong>
                </article>
                <article class="metric-card">
                  <span>Total completed sales</span>
                  <strong>{{ formatMoney(vendorSales.summary.total_sales) }}</strong>
                </article>
              </div>

              <div class="vendor-panel">
                <div class="vendor-panel-head">
                  <h2>Popular Items</h2>
                  <span class="muted-text">Completed orders only</span>
                </div>
                <table class="vendor-table">
                  <thead>
                    <tr>
                      <th>Item</th>
                      <th>Category</th>
                      <th>Quantity Sold</th>
                      <th>Sales</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in vendorSales.popular_items" :key="item.item_id">
                      <td>{{ item.name }}</td>
                      <td>{{ item.category }}</td>
                      <td>{{ item.total_quantity }}</td>
                      <td>{{ formatMoney(item.total_sales) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </template>
          </section>

          <section v-else class="page route-panel">
            <div class="empty-panel">
              <div>
                <p class="eyebrow">Placeholder route</p>
                <h1>{{ currentRoute.title }}</h1>
                <p>{{ currentRoute.note }}</p>
                <div class="link-list">
                  <a class="pill-button primary" href="#/home">Back Home</a>
                  <a class="pill-button" href="#/menu">Menu</a>
                  <a class="pill-button" href="#/cart">Cart</a>
                  <a class="pill-button" href="#/orders">Orders</a>
                </div>
              </div>
            </div>
          </section>

          <footer class="footer">
            Universal Sambal base UI. Vue frontend now, Slim API routes later.
          </footer>
        </main>
      `
    }).mount('#app');
  </script>
</body>

</html>

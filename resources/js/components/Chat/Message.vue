<template>
    <div class="message" :class="message.dir == 1 ? 'message-out': 'message-in'" v-if="message.type=='text'">
        <div class="message-content">
            <div class="message-time">{{ message.time }}</div>
            <div class="message-text">{{ message.content }}</div>
        </div>
    </div>
    <div class="message" :class="message.dir == 1 ? 'message-out': 'message-in'" v-else>
        <div class="message-content voice">
            <div class="message-time">{{ message.time }}</div>
            <div class="message-voice">
                <div class="voice-icon">
                    <svg width="82" height="31" viewBox="0 0 82 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M70.0114 16.7317C70.3136 16.724 70.6015 16.5922 70.8153 16.3637C71.0291 16.1352 71.1524 15.8276 71.1597 15.5046V14.6852C71.1597 14.3597 71.0387 14.0476 70.8233 13.8174C70.608 13.5873 70.3159 13.458 70.0114 13.458C69.7068 13.458 69.4147 13.5873 69.1994 13.8174C68.984 14.0476 68.8631 14.3597 68.8631 14.6852V15.5046C68.8703 15.8276 68.9937 16.1352 69.2074 16.3637C69.4212 16.5922 69.7091 16.724 70.0114 16.7317Z" fill="url(#paint0_linear_4790_15294)"/>
                        <path d="M75.1483 16.7317C75.4506 16.724 75.7384 16.5922 75.9522 16.3637C76.166 16.1352 76.2894 15.8276 76.2966 15.5046V14.6852C76.2966 14.3597 76.1756 14.0476 75.9603 13.8174C75.7449 13.5873 75.4529 13.458 75.1483 13.458C74.8437 13.458 74.5517 13.5873 74.3363 13.8174C74.121 14.0476 74 14.3597 74 14.6852V15.5046C74.0072 15.8276 74.1306 16.1352 74.3444 16.3637C74.5582 16.5922 74.846 16.724 75.1483 16.7317Z" fill="url(#paint1_linear_4790_15294)"/>
                        <path d="M79.892 16.7317C80.1942 16.724 80.4821 16.5922 80.6959 16.3637C80.9097 16.1352 81.033 15.8276 81.0403 15.5046V14.6852C81.0403 14.3597 80.9193 14.0476 80.704 13.8174C80.4886 13.5873 80.1965 13.458 79.892 13.458C79.5874 13.458 79.2954 13.5873 79.08 13.8174C78.8647 14.0476 78.7437 14.3597 78.7437 14.6852V15.5046C78.751 15.8276 78.8743 16.1352 79.0881 16.3637C79.3019 16.5922 79.5897 16.724 79.892 16.7317Z" fill="url(#paint2_linear_4790_15294)"/>
                        <path d="M65.4164 16.3222C65.7205 16.3012 66.0127 16.1876 66.2583 15.9948L66.4124 15.8321C66.441 15.7997 66.4625 15.7609 66.4755 15.7185C66.4885 15.6762 66.4926 15.6313 66.4876 15.5871C66.4876 15.5048 66.5647 15.4224 66.5647 15.3401V14.8558C66.57 14.8114 66.5658 14.7663 66.5524 14.7238C66.5391 14.6814 66.5169 14.6427 66.4876 14.6107C66.4876 14.5284 66.4124 14.4461 66.4124 14.3638C66.3354 14.2834 66.3354 14.2011 66.2583 14.2011C66.1208 14.0521 65.9495 13.9437 65.7612 13.8863C65.5729 13.829 65.3739 13.8246 65.1835 13.8737C65.1417 13.8682 65.0992 13.8727 65.0592 13.8869C65.0192 13.9012 64.9827 13.9248 64.9524 13.956C64.8772 13.956 64.8001 14.0364 64.7231 14.0364C64.6834 14.0396 64.6461 14.058 64.6181 14.0883C64.5901 14.1185 64.5733 14.1586 64.5708 14.2011L64.4168 14.3638C64.4168 14.4461 64.3397 14.5284 64.3397 14.6107C64.3397 14.6931 64.2645 14.7735 64.2645 14.8558V15.3459C64.2593 15.3904 64.2634 15.4356 64.2764 15.4783C64.2894 15.521 64.311 15.5601 64.3397 15.5928C64.3397 15.6732 64.4168 15.7555 64.4168 15.8379L64.5708 16.0006C64.8181 16.192 65.1115 16.3036 65.4164 16.3222Z" fill="url(#paint3_linear_4790_15294)"/>
                        <path d="M60.8214 22.0521C61.1237 22.0443 61.4115 21.9125 61.6253 21.6841C61.8391 21.4556 61.9625 21.148 61.9697 20.8249V9.36514C61.958 9.04782 61.8318 8.74766 61.6176 8.52758C61.4033 8.30749 61.1177 8.18457 60.8205 8.18457C60.5233 8.18457 60.2377 8.30749 60.0235 8.52758C59.8092 8.74766 59.683 9.04782 59.6713 9.36514V20.8249C59.679 21.1481 59.8027 21.4558 60.0168 21.6842C60.2309 21.9126 60.519 22.0443 60.8214 22.0521Z" fill="url(#paint4_linear_4790_15294)"/>
                        <path d="M51.6295 26.1502C51.9318 26.1424 52.2196 26.0106 52.4334 25.7821C52.6472 25.5537 52.7706 25.246 52.7778 24.923V5.28282C52.7778 4.95736 52.6568 4.64523 52.4415 4.41509C52.2261 4.18496 51.9341 4.05566 51.6295 4.05566C51.325 4.05566 51.0329 4.18496 50.8175 4.41509C50.6022 4.64523 50.4812 4.95736 50.4812 5.28282V24.9173C50.4871 25.2413 50.6098 25.5504 50.8238 25.7801C51.0377 26.0097 51.3263 26.1423 51.6295 26.1502Z" fill="url(#paint5_linear_4790_15294)"/>
                        <path d="M56.2246 30.2378C56.5272 30.2295 56.8153 30.0974 57.0294 29.8687C57.2434 29.6399 57.367 29.3321 57.3748 29.0087V1.18057C57.363 0.863245 57.2368 0.563088 57.0226 0.343006C56.8083 0.122924 56.5227 0 56.2255 0C55.9284 0 55.6427 0.122924 55.4285 0.343006C55.2143 0.563088 55.0881 0.863245 55.0763 1.18057V29.0087C55.0836 29.3319 55.2068 29.6398 55.4206 29.8685C55.6343 30.0973 55.9222 30.2295 56.2246 30.2378Z" fill="url(#paint6_linear_4790_15294)"/>
                        <path d="M47.0346 21.2346C47.337 21.2263 47.6249 21.0941 47.8386 20.8653C48.0524 20.6366 48.1756 20.3287 48.1829 20.0055V10.1845C48.1711 9.86715 48.0449 9.56699 47.8307 9.34691C47.6165 9.12683 47.3308 9.00391 47.0337 9.00391C46.7365 9.00391 46.4509 9.12683 46.2367 9.34691C46.0224 9.56699 45.8962 9.86715 45.8845 10.1845V20.0055C45.8922 20.3289 46.0158 20.6367 46.2299 20.8655C46.4439 21.0942 46.732 21.2263 47.0346 21.2346Z" fill="url(#paint7_linear_4790_15294)"/>
                        <path d="M42.4396 18.3679C42.7418 18.3601 43.0297 18.2284 43.2435 17.9999C43.4573 17.7714 43.5806 17.4638 43.5879 17.1408V13.0477C43.5762 12.7304 43.45 12.4303 43.2357 12.2102C43.0215 11.9901 42.7359 11.8672 42.4387 11.8672C42.1415 11.8672 41.8559 11.9901 41.6417 12.2102C41.4274 12.4303 41.3012 12.7304 41.2895 13.0477V17.1408C41.2968 17.4641 41.4204 17.772 41.6345 18.0005C41.8487 18.2291 42.137 18.3606 42.4396 18.3679Z" fill="url(#paint8_linear_4790_15294)"/>
                        <path d="M37.8426 19.1877C38.1451 19.18 38.4332 19.0483 38.6472 18.8198C38.8613 18.5914 38.985 18.2838 38.9928 17.9606V12.2307C38.9989 12.0653 38.9737 11.9004 38.9187 11.7457C38.8637 11.591 38.78 11.4497 38.6727 11.3304C38.5654 11.2111 38.4366 11.1162 38.2941 11.0514C38.1517 10.9865 37.9984 10.9531 37.8435 10.9531C37.6887 10.9531 37.5354 10.9865 37.3929 11.0514C37.2505 11.1162 37.1217 11.2111 37.0144 11.3304C36.9071 11.4497 36.8234 11.591 36.7684 11.7457C36.7134 11.9004 36.6882 12.0653 36.6943 12.2307V17.9606C36.7016 18.2836 36.8249 18.5912 37.0387 18.8197C37.2525 19.0482 37.5404 19.18 37.8426 19.1877Z" fill="url(#paint9_linear_4790_15294)"/>
                        <path d="M33.255 20.4071C33.5573 20.3994 33.8451 20.2676 34.0589 20.0391C34.2727 19.8106 34.396 19.503 34.4033 19.18V11.0016C34.4033 10.8404 34.3736 10.6808 34.3159 10.532C34.2582 10.3831 34.1736 10.2478 34.067 10.1338C33.9604 10.0199 33.8338 9.9295 33.6944 9.86783C33.5551 9.80616 33.4058 9.77441 33.255 9.77441C33.1042 9.77441 32.9549 9.80616 32.8155 9.86783C32.6762 9.9295 32.5496 10.0199 32.443 10.1338C32.3364 10.2478 32.2518 10.3831 32.1941 10.532C32.1364 10.6808 32.1067 10.8404 32.1067 11.0016V19.1877C32.1157 19.5093 32.2399 19.815 32.4535 20.0419C32.6671 20.2687 32.9539 20.3995 33.255 20.4071Z" fill="url(#paint10_linear_4790_15294)"/>
                        <path d="M28.6527 17.9606C28.9551 17.9524 29.243 17.8202 29.4568 17.5914C29.6705 17.3626 29.7938 17.0548 29.801 16.7316V13.4579C29.7893 13.1406 29.6631 12.8404 29.4489 12.6203C29.2346 12.4003 28.949 12.2773 28.6518 12.2773C28.3547 12.2773 28.069 12.4003 27.8548 12.6203C27.6406 12.8404 27.5144 13.1406 27.5026 13.4579V16.7316C27.5103 17.055 27.634 17.3628 27.848 17.5915C28.062 17.8203 28.3501 17.9524 28.6527 17.9606Z" fill="url(#paint11_linear_4790_15294)"/>
                        <path d="M24.0559 17.5504C24.3588 17.5431 24.6474 17.4113 24.8616 17.1823C25.0758 16.9534 25.1992 16.645 25.2061 16.3214V13.8671C25.1943 13.5498 25.0681 13.2496 24.8539 13.0295C24.6396 12.8094 24.354 12.6865 24.0568 12.6865C23.7597 12.6865 23.474 12.8094 23.2598 13.0295C23.0456 13.2496 22.9194 13.5498 22.9076 13.8671V16.3214C22.9144 16.6447 23.0376 16.9528 23.2514 17.1817C23.4653 17.4106 23.7534 17.5426 24.0559 17.5504Z" fill="url(#paint12_linear_4790_15294)"/>
                        <path d="M19.4609 16.3219H19.6903C19.7319 16.3274 19.7742 16.3231 19.8142 16.3092C19.8541 16.2953 19.8907 16.2722 19.9214 16.2415C19.9966 16.2415 20.0736 16.1592 20.1507 16.1592C20.2277 16.0768 20.3029 16.0769 20.3029 15.9945L20.457 15.8318C20.457 15.7495 20.534 15.6672 20.534 15.5868C20.534 15.5064 20.6111 15.4221 20.6111 15.3398V15.0948C20.5908 14.7695 20.4839 14.4573 20.3029 14.195L20.1507 14.0303C20.1203 13.9998 20.084 13.9768 20.0444 13.9629C20.0048 13.949 19.9628 13.9446 19.9214 13.9499C19.8443 13.9499 19.7673 13.8676 19.6903 13.8676H19.2316C19.1901 13.862 19.1479 13.8664 19.1082 13.8807C19.0684 13.8949 19.0323 13.9186 19.0023 13.9499C18.9253 13.9499 18.8483 14.0303 18.7712 14.0303C18.7315 14.0335 18.6942 14.0519 18.6662 14.0822C18.6382 14.1124 18.6214 14.1525 18.619 14.195C18.5142 14.3155 18.4332 14.4572 18.3806 14.6117C18.3279 14.7663 18.3048 14.9305 18.3126 15.0948V15.3398C18.3074 15.3845 18.3117 15.4299 18.325 15.4726C18.3383 15.5153 18.3604 15.5543 18.3896 15.5868C18.3896 15.6672 18.4649 15.7495 18.4649 15.8318L18.619 15.9945L18.7712 16.1592C18.8483 16.1592 18.9253 16.2415 19.0023 16.2415C19.0794 16.2415 19.1546 16.3219 19.2316 16.3219H19.4609Z" fill="url(#paint13_linear_4790_15294)"/>
                        <path d="M14.8658 16.7317C15.1681 16.724 15.456 16.5922 15.6698 16.3637C15.8836 16.1352 16.0069 15.8276 16.0142 15.5046V14.6852C16.0142 14.3597 15.8932 14.0476 15.6778 13.8174C15.4625 13.5873 15.1704 13.458 14.8658 13.458C14.5613 13.458 14.2692 13.5873 14.0539 13.8174C13.8385 14.0476 13.7175 14.3597 13.7175 14.6852V15.5046C13.7248 15.8276 13.8481 16.1352 14.0619 16.3637C14.2757 16.5922 14.5636 16.724 14.8658 16.7317Z" fill="url(#paint14_linear_4790_15294)"/>
                        <path d="M5.66681 16.7311C5.96924 16.7233 6.2573 16.5916 6.47138 16.3632C6.68546 16.1347 6.80917 15.8271 6.81689 15.5039V14.6845C6.80517 14.3672 6.67897 14.067 6.46473 13.8469C6.2505 13.6268 5.96487 13.5039 5.66771 13.5039C5.37054 13.5039 5.08491 13.6268 4.87068 13.8469C4.65645 14.067 4.53025 14.3672 4.51852 14.6845V15.5039C4.52579 15.8269 4.6491 16.1345 4.86289 16.363C5.07668 16.5915 5.36455 16.7233 5.66681 16.7311Z" fill="url(#paint15_linear_4790_15294)"/>
                        <path d="M10.2709 16.3217C10.575 16.3007 10.8672 16.1871 11.1129 15.9943L11.2651 15.8316C11.2651 15.7493 11.3422 15.667 11.3422 15.5866C11.3422 15.5062 11.4192 15.4219 11.4192 15.3396V15.0946C11.3987 14.7697 11.2925 14.4577 11.1129 14.1948C11.0358 14.1125 10.9588 14.1125 10.9588 14.0301C10.9285 13.9994 10.8923 13.9762 10.8526 13.9623C10.813 13.9484 10.7709 13.9441 10.7295 13.9497C10.6525 13.9497 10.5772 13.8674 10.5002 13.8674H10.0398C9.99823 13.862 9.9561 13.8666 9.91643 13.8809C9.87676 13.8951 9.84056 13.9186 9.81048 13.9497C9.73345 13.9497 9.6582 14.0301 9.58117 14.0301C9.54114 14.0328 9.50342 14.0511 9.47506 14.0814C9.44669 14.1117 9.42965 14.152 9.42711 14.1948C9.3224 14.3153 9.24134 14.457 9.18872 14.6115C9.13611 14.7661 9.11301 14.9303 9.12078 15.0946V15.3396C9.11561 15.3843 9.11983 15.4297 9.13316 15.4724C9.14648 15.5151 9.16858 15.5541 9.19781 15.5866C9.19781 15.667 9.27484 15.7493 9.27484 15.8316L9.4289 16.0039L9.58296 16.1685C9.66 16.1685 9.73524 16.2509 9.81227 16.2509C9.88931 16.2509 9.96633 16.3313 10.0416 16.3313L10.2709 16.3217Z" fill="url(#paint16_linear_4790_15294)"/>
                        <path d="M1.07899 16.3219C1.38314 16.3009 1.67531 16.1873 1.92097 15.9945L2.07503 15.8318C2.0831 15.7693 2.11017 15.7115 2.15206 15.6671C2.15206 15.5867 2.22729 15.5044 2.22729 15.4221V15.177C2.20766 14.852 2.10137 14.5397 1.92097 14.2772C1.46236 13.7852 0.695629 13.7852 0.30868 14.2772C0.203626 14.3975 0.122341 14.5391 0.0697055 14.6938C0.0170703 14.8484 -0.00583792 15.0127 0.00234103 15.177V15.4297C-0.00291944 15.474 0.00110316 15.5189 0.014117 15.5613C0.0271308 15.6037 0.0488069 15.6425 0.0775781 15.6748C0.0856545 15.7372 0.112722 15.7951 0.154608 15.8394C0.23164 15.9217 0.231648 16.0021 0.30868 16.0021C0.520062 16.2061 0.794217 16.3199 1.07899 16.3219Z" fill="url(#paint17_linear_4790_15294)"/>
                        <defs>
                        <linearGradient id="paint0_linear_4790_15294" x1="70.0114" y1="13.458" x2="70.0114" y2="16.7317" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint1_linear_4790_15294" x1="75.1483" y1="13.458" x2="75.1483" y2="16.7317" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint2_linear_4790_15294" x1="79.892" y1="13.458" x2="79.892" y2="16.7317" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint3_linear_4790_15294" x1="65.4146" y1="13.8398" x2="65.4146" y2="16.3222" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint4_linear_4790_15294" x1="60.8205" y1="8.18457" x2="60.8205" y2="22.0521" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint5_linear_4790_15294" x1="51.6295" y1="4.05566" x2="51.6295" y2="26.1502" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint6_linear_4790_15294" x1="56.2255" y1="0" x2="56.2255" y2="30.2378" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint7_linear_4790_15294" x1="47.0337" y1="9.00391" x2="47.0337" y2="21.2346" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint8_linear_4790_15294" x1="42.4387" y1="11.8672" x2="42.4387" y2="18.3679" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint9_linear_4790_15294" x1="37.8435" y1="10.9531" x2="37.8435" y2="19.1877" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint10_linear_4790_15294" x1="33.255" y1="9.77441" x2="33.255" y2="20.4071" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint11_linear_4790_15294" x1="28.6518" y1="12.2773" x2="28.6518" y2="17.9606" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint12_linear_4790_15294" x1="24.0568" y1="12.6865" x2="24.0568" y2="17.5504" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint13_linear_4790_15294" x1="19.4608" y1="13.8652" x2="19.4608" y2="16.3242" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint14_linear_4790_15294" x1="14.8658" y1="13.458" x2="14.8658" y2="16.7317" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint15_linear_4790_15294" x1="5.66771" y1="13.5039" x2="5.66771" y2="16.7311" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint16_linear_4790_15294" x1="10.2689" y1="13.8652" x2="10.2689" y2="16.3313" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        <linearGradient id="paint17_linear_4790_15294" x1="1.1137" y1="13.9082" x2="1.1137" y2="16.3219" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#2E2C4E" stop-opacity="0.53"/>
                        <stop offset="0.432292" stop-color="#175159" stop-opacity="0.762552"/>
                        <stop offset="0.9999" stop-color="#17525A" stop-opacity="0.769087"/>
                        <stop offset="1" stop-color="#007665"/>
                        </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="voice-play" @click="playAudio(message.content)" v-if="!pause">
                    <svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 5L0.5 9.33013L0.5 0.669872L8 5Z" fill="#55A99D"/>
                    </svg>
                </div>
                <div class="voice-play" @click="pauseAudio" v-else>
                    <svg version="1.1" width="15" height="15" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.88 122.88" style="enable-background:new 0 0 122.88 122.88" xml:space="preserve">
                        <g>
                            <path d="M61.44,0c16.97,0,32.33,6.88,43.44,18c11.12,11.12,18,26.48,18,43.44c0,16.97-6.88,32.33-18,43.44 c-11.12,11.12-26.48,18-43.44,18c-16.97,0-32.33-6.88-43.44-18C6.88,93.77,0,78.41,0,61.44C0,44.47,6.88,29.11,18,18 C29.11,6.88,44.47,0,61.44,0L61.44,0z M42.3,39.47h13.59v43.95l-13.59,0V39.47L42.3,39.47L42.3,39.47z M66.99,39.47h13.59v43.95 l-13.59,0V39.47L66.99,39.47L66.99,39.47z M97.42,25.46c-9.21-9.21-21.93-14.9-35.98-14.9c-14.05,0-26.78,5.7-35.98,14.9 c-9.21,9.21-14.9,21.93-14.9,35.98s5.7,26.78,14.9,35.98c9.21,9.21,21.93,14.9,35.98,14.9c14.05,0,26.78-5.7,35.98-14.9 c9.21-9.21,14.9-21.93,14.9-35.98S106.63,34.66,97.42,25.46L97.42,25.46z"/>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { ref } from 'vue';
export default{
    props:{
        message:{
            type: Object,
            required: true,
        },
    },
    setup(){
        const audioElement = ref(null);
        const pause = ref(false);
        const playAudio = (audioUrlObject)=>{
            pause.value = ! pause.value
            audioElement.value = new Audio(audioUrlObject);
            audioElement.value.play();
            audioElement.value.addEventListener('ended', function(e){
                pause.value = ! pause.value
            }, false);            
        }
        const pauseAudio = ()=>{
            pause.value = ! pause.value
            audioElement.value.pause();
        }
        return{
            pause,
            playAudio,
            pauseAudio,
        }
    }
}
</script>
<style scoped>
.message-time{
    font-family: 'Poppins';
    font-style: italic;
    font-weight: 300;
    font-size: 9px;
    line-height: 14px;
    color: #4E4E4E;
}
.message-in .message-text{
    height: 40px;
    padding: 10px 13px;
    background: rgba(228, 228, 228, 0.83);
    border-radius: 20px 20px 20px 0px;
}
.message-out .message-text{
    height: 40px;
    padding: 10px 13px;
    background: rgba(255, 185, 28, 0.5);
    border-radius: 20px 20px 0px 20px;
}
.message {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: end;
    -ms-flex-align: end;
    align-items: flex-end;
}
.message-content {
    width: 200px;
}
.message-content.voice {
    width: 170px;
}
.message+.message {
    margin-top: 1rem;
}
.message-out {
    -webkit-box-orient: horizontal;
    -webkit-box-direction: reverse;
    -ms-flex-direction: row-reverse;
    flex-direction: row-reverse;
}
.message-in {
    -webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
}
.message-in .message-voice{
    height: 55px;
    padding: 10px 13px;
    background: rgba(228, 228, 228, 0.83);
    border-radius: 20px 20px 20px 0px;
}
.message-out .message-voice{
    height: 55px;
    padding: 10px 13px;
    background: rgba(255, 185, 28, 0.6);
    border-radius: 27.5px 27.5px 0px 27.5px;
}
.message-voice{
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.voice-play{
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.voice-play svg{
    transform: scale(1.5);
}
</style>
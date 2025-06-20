<script setup>

</script>

<template>
    <div class="block h-[140px] relative">
        <div class="loader-wrapper">
            <ul class="loader-books">
                <li class="loader-book first" />
                <li class="loader-book second" />
                <li class="loader-book third" />
                <li class="loader-book fourth" />
                <li class="loader-book fifth" />
                <li class="loader-book sixth" />
            </ul>
        </div>
    </div>
</template>

<style scoped>
.loader-wrapper {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    height: 100%;
    display: block;
    --main-colour: #7c2d12;
}

.loader-books {
    margin: 0 auto;
    width: 300px;
    padding: 0;
}

.loader-book {
    position: absolute;
    top: 20px;
    box-sizing: border-box;
    list-style: none;
    width: 40px;
    height: 120px;
    opacity: 0;
    background-color: var(--main-colour);
    border: 5px solid white;
    transform-origin: bottom left;
    transform: translateX(300px);
    -webkit-animation: travel 2500ms linear infinite;
    animation: travel 2500ms linear infinite;
}

.loader-book.first {
    top: 0;
    height: 140px;
}

.loader-book.first:before, .loader-book.first:after {
    content: "";
    position: absolute;
    top: 10px;
    left: 0;
    width: 100%;
    height: 5px;
    background-color: white;
}

.loader-book.first:after {
    top: initial;
    bottom: 10px;
}

.loader-book.second:before, .loader-book.second:after, .loader-book.fifth:before, .loader-book.fifth:after {
    box-sizing: border-box;
    content: "";
    position: absolute;
    top: 10px;
    left: 0;
    width: 100%;
    height: 17.5px;
    border-top: 5px solid white;
    border-bottom: 5px solid white;
}

.loader-book.second:after, .loader-book.fifth:after {
    top: initial;
    bottom: 10px;
}

.loader-book.third:before, .loader-book.third:after {
    box-sizing: border-box;
    content: "";
    position: absolute;
    top: 10px;
    left: 9px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 5px solid white;
}

.loader-book.third:after {
    top: initial;
    bottom: 10px;
}

.loader-book.fourth {
    top: 10px;
    height: 130px;
}

.loader-book.fourth:before {
    box-sizing: border-box;
    content: "";
    position: absolute;
    top: 46px;
    left: 0;
    width: 100%;
    height: 17.5px;
    border-top: 5px solid white;
    border-bottom: 5px solid white;
}

.loader-book.fifth {
    top: 40px;
    height: 100px;
}

.loader-book.sixth {
    top: 0px;
    height: 140px;
}

.loader-book.sixth:before {
    box-sizing: border-box;
    content: "";
    position: absolute;
    bottom: 31px;
    left: 0px;
    width: 100%;
    height: 5px;
    background-color: white;
}

.loader-book.sixth:after {
    box-sizing: border-box;
    content: "";
    position: absolute;
    bottom: 10px;
    left: 9px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 5px solid white;
}

.loader-book:nth-child(2) {
    -webkit-animation-delay: 416.6666666667ms;
    animation-delay: 416.6666666667ms;
}

.loader-book:nth-child(3) {
    -webkit-animation-delay: 833.3333333333ms;
    animation-delay: 833.3333333333ms;
}

.loader-book:nth-child(4) {
    -webkit-animation-delay: 1250ms;
    animation-delay: 1250ms;
}

.loader-book:nth-child(5) {
    -webkit-animation-delay: 1666.6666666667ms;
    animation-delay: 1666.6666666667ms;
}

.loader-book:nth-child(6) {
    -webkit-animation-delay: 2083.3333333333ms;
    animation-delay: 2083.3333333333ms;
}

@-webkit-keyframes travel {
    0% {
        opacity: 0;
        transform: translateX(300px) rotateZ(0deg) scaleY(1);
    }
    6.5% {
        transform: translateX(279.5px) rotateZ(0deg) scaleY(1.1);
    }
    8.8% {
        transform: translateX(273.6px) rotateZ(0deg) scaleY(1);
    }
    10% {
        opacity: 1;
        transform: translateX(270px) rotateZ(0deg);
    }
    17.6% {
        transform: translateX(247.2px) rotateZ(-30deg);
    }
    45% {
        transform: translateX(165px) rotateZ(-30deg);
    }
    49.5% {
        transform: translateX(151.5px) rotateZ(-45deg);
    }
    61.5% {
        transform: translateX(115.5px) rotateZ(-45deg);
    }
    67% {
        transform: translateX(99px) rotateZ(-60deg);
    }
    76% {
        transform: translateX(72px) rotateZ(-60deg);
    }
    83.5% {
        opacity: 1;
        transform: translateX(49.5px) rotateZ(-90deg);
    }
    90% {
        opacity: 0;
    }
    100% {
        opacity: 0;
        transform: translateX(0px) rotateZ(-90deg);
    }
}

@keyframes travel {
    0% {
        opacity: 0;
        transform: translateX(300px) rotateZ(0deg) scaleY(1);
    }
    6.5% {
        transform: translateX(279.5px) rotateZ(0deg) scaleY(1.1);
    }
    8.8% {
        transform: translateX(273.6px) rotateZ(0deg) scaleY(1);
    }
    10% {
        opacity: 1;
        transform: translateX(270px) rotateZ(0deg);
    }
    17.6% {
        transform: translateX(247.2px) rotateZ(-30deg);
    }
    45% {
        transform: translateX(165px) rotateZ(-30deg);
    }
    49.5% {
        transform: translateX(151.5px) rotateZ(-45deg);
    }
    61.5% {
        transform: translateX(115.5px) rotateZ(-45deg);
    }
    67% {
        transform: translateX(99px) rotateZ(-60deg);
    }
    76% {
        transform: translateX(72px) rotateZ(-60deg);
    }
    83.5% {
        opacity: 1;
        transform: translateX(49.5px) rotateZ(-90deg);
    }
    90% {
        opacity: 0;
    }
    100% {
        opacity: 0;
        transform: translateX(0px) rotateZ(-90deg);
    }
}
</style>

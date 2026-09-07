const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

if (typeof PureCounter !== 'undefined') {
    const pure = new PureCounter();
    new PureCounter();
}

// general js project js
$(function() {
    "use strict";
    const root = document.documentElement;

    // main sidebar menu toggle js
    $('.sidebar-toggle-btn').on('click', function () {
		$('body').toggleClass('sidebar-hide')
	});

    window.themePalettes = {
        'ValenciaRed': { primary: '#DE3641', secondary: '#FF6B72', bar: '#DE3641', line: '#20c997' },
        'SunOrange': { primary: '#F7A614', secondary: '#FFC15E', bar: '#F7A614', line: '#00B8D6' },
        'AppleGreen': { primary: '#5BC43A', secondary: '#86E06A', bar: '#5BC43A', line: '#6238B3' },
        'CeruleanBlue': { primary: '#00B8D6', secondary: '#33D1EC', bar: '#00B8D6', line: '#DE3641' },
        'Mariner': { primary: '#0066FE', secondary: '#4D94FF', bar: '#0066FE', line: '#F7A614' },
        'PurpleHeart': { primary: '#6238B3', secondary: '#8D65DC', bar: '#6238B3', line: '#00B8D6' },
        'FrenchRose': { primary: '#EB5393', secondary: '#F384B4', bar: '#EB5393', line: '#5BC43A' }
    };

    window.getThemeColors = function() {
        const savedTheme = localStorage.getItem('hotelair_theme') || 'PurpleHeart';
        const palette = window.themePalettes[savedTheme] || window.themePalettes['PurpleHeart'];
        return [palette.bar, palette.line];
    };

    window.updateApexThemeColors = function() {
        if (window.reservationChart && typeof window.reservationChart.updateOptions === 'function') {
            const colors = window.getThemeColors();
            window.reservationChart.updateOptions({
                colors: colors
            });
        }
    };

    // Main theme color setting with localStorage persistence
    function applySavedThemeSettings() {
        const savedTheme = localStorage.getItem('hotelair_theme') || 'PurpleHeart';
        $('body').attr('data-theme', 'theme-' + savedTheme);
        $('.choose-skin li').removeClass('active');
        $('.choose-skin li[data-theme="' + savedTheme + '"]').addClass('active');

        const palette = window.themePalettes[savedTheme] || window.themePalettes['PurpleHeart'];
        document.documentElement.style.setProperty('--theme-color1', palette.primary);
        document.documentElement.style.setProperty('--theme-color4', palette.bar);
        document.documentElement.style.setProperty('--theme-color5', palette.line);
        document.documentElement.style.setProperty('--hotelair-theme-color', palette.primary);

        if (localStorage.getItem('hotelair_monochrome') === 'true') {
            $('body').addClass('monochrome');
            $('#monochrome').prop('checked', true);
        }

        if (localStorage.getItem('hotelair_radius0') === 'true') {
            $('body').addClass('radius-0');
            $('#radius0').prop('checked', true);
        }

        if (localStorage.getItem('hotelair_iconcolor') === 'true') {
            $('.menu-list').addClass('icon-color');
            $('#IconColor').prop('checked', true);
        }

        if (localStorage.getItem('hotelair_boxshadow') === 'true') {
            $('.card').addClass('shadow-active');
            $('#BoxShadow').prop('checked', true);
        }

        if (localStorage.getItem('hotelair_sidebar_dark') === 'true') {
            $('.sidebar').attr('data-bs-theme', 'dark');
            $('#Sidebar_dark').prop('checked', true);
        }

        if (localStorage.getItem('hotelair_header_dark') === 'true') {
            $('header').attr('data-bs-theme', 'dark');
            $('#Header_dark').prop('checked', true);
        }

        const savedStroke = localStorage.getItem('hotelair_stroke');
        if (savedStroke) {
            $('body').removeClass('svgstroke-a svgstroke-b svgstroke-c').addClass(savedStroke);
            $('input[name="stroke"][value="' + savedStroke + '"]').prop('checked', true);
        }
    }

    applySavedThemeSettings();

    $('.choose-skin li').on('click', function() {
        const $this = $(this);
        const themeName = $this.data('theme');
        $('.choose-skin li').removeClass('active');
        $this.addClass('active');
        $('body').attr('data-theme', 'theme-' + themeName);
        localStorage.setItem('hotelair_theme', themeName);

        const palette = window.themePalettes[themeName] || window.themePalettes['PurpleHeart'];
        document.documentElement.style.setProperty('--theme-color1', palette.primary);
        document.documentElement.style.setProperty('--theme-color4', palette.bar);
        document.documentElement.style.setProperty('--theme-color5', palette.line);
        document.documentElement.style.setProperty('--hotelair-theme-color', palette.primary);

        window.updateApexThemeColors();
    });

    // Monochrome Mode
    $('.monochrome-toggle input:checkbox, #monochrome').on('change', function () {
        if($(this).is(":checked")) {
            $('body').addClass("monochrome");
            localStorage.setItem('hotelair_monochrome', 'true');
        } else {
            $('body').removeClass("monochrome");
            localStorage.setItem('hotelair_monochrome', 'false');
        }
    });

    // radius-0
    $('.radius-toggle input:checkbox, #radius0').on('change', function () {
        if($(this).is(":checked")) {
            $('body').addClass("radius-0");
            localStorage.setItem('hotelair_radius0', 'true');
        } else {
            $('body').removeClass("radius-0");
            localStorage.setItem('hotelair_radius0', 'false');
        }
    });

    // Layout section light/dark toggle js
    $('.sidebar-toggle input:checkbox, #Sidebar_dark').on('change', function () {
        if ($(this).is(':checked')) {
            $('.sidebar').attr('data-bs-theme', 'dark');
            localStorage.setItem('hotelair_sidebar_dark', 'true');
        } else {
            $('.sidebar').attr('data-bs-theme', 'none');
            localStorage.setItem('hotelair_sidebar_dark', 'false');
        }
    });

    $('.header-toggle input:checkbox, #Header_dark').on('change', function () {
        if ($(this).is(':checked')) {
            $('header').attr('data-bs-theme', 'dark');
            localStorage.setItem('hotelair_header_dark', 'true');
        } else {
            $('header').attr('data-bs-theme', 'none');
            localStorage.setItem('hotelair_header_dark', 'false');
        }
    });

    // svg icon stroke
    $('.svg-stroke input:radio').on('click', function () {
        var others = $("[name='" + this.name + "']").map(function () {
            return this.value;
        }).get().join(" ");
        $('body').removeClass(others).addClass(this.value);
        localStorage.setItem('hotelair_stroke', this.value);
    });

    // sidebar icon color
    $('.svg-icon-color input:checkbox, #IconColor').on('change', function () {
        if ($(this).is(':checked')) {
            $('.menu-list').addClass('icon-color');
            localStorage.setItem('hotelair_iconcolor', 'true');
        } else {
            $('.menu-list').removeClass('icon-color');
            localStorage.setItem('hotelair_iconcolor', 'false');
        }
    });

    // card box shadow
    $('.cb-shadow input:checkbox, #BoxShadow').on('change', function () {
        if ($(this).is(':checked')) {
            $('.card').addClass('shadow-active');
            localStorage.setItem('hotelair_boxshadow', 'true');
        } else {
            $('.card').removeClass('shadow-active');
            localStorage.setItem('hotelair_boxshadow', 'false');
        }
    });
    

    // Password Meter
    $(".password-meter .form-control").on("input", function() {
        var matchedCriteria = 0;            
        var password = $(this).val();
        var upperCase= new RegExp('[A-Z]');
        var lowerCase= new RegExp('[a-z]');
        var numbers = new RegExp('[0-9]');
        var specialChars = new RegExp('^(?=.*?[#?!@$%^&*-]).{1,}$');
        if(password.length > 7) {
            matchedCriteria++;
        }
        if(password.length > 0 && password.match(upperCase)) {
            matchedCriteria++;
        }
        if(password.length > 0 && password.match(lowerCase)) {
            matchedCriteria++;
        }
        if(password.length > 0 && password.match(numbers)) {
            matchedCriteria++;
        }
        if(password.length > 0 && password.match(specialChars)) {
            matchedCriteria++;
        }
        $(".password-meter .progress-bar")[0].style.width = (matchedCriteria*20)+"%";
    })
    // Image file input
    $(".image-input .form-control").on("change",function(){
        var url = URL.createObjectURL(this.files[0]);
        $(this).parent().parent().children(".avatar-wrapper")[0].style.background = "url(" + url + ") no-repeat";
    })
    // table select all checkbox js
    $(".select-all.form-check-input").on('change', function ()  {
        //row-selectable
        var isChecked = $(this).is(":checked")
        var rows = $(this).parent().parent().parent().parent().parent().find(".row-selectable");
        if(rows.length > 0) {
            rows.each(function( index ) {
                $( this ).find(".form-check-input")[0].checked = isChecked;
            }); 
        }
    })

    $('.deleterow').on('click',function(){       
    var tablename = $(this).closest('table').DataTable();  
    tablename
        .row( $(this)
        .parents('tr'))
        .remove()
        .draw();  
    } ); 
    
});

// Light/Dark
/*!
* Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
* Copyright 2011-2022 The Bootstrap Authors
* Licensed under the Creative Commons Attribution 3.0 Unported License.
*/

(() => {
    'use strict'
    const storedTheme = localStorage.getItem('theme')
    const getPreferredTheme = () => {
        if (storedTheme) {
            return storedTheme
        }
    
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }
    const setTheme = function (theme) {
        if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-bs-theme', 'dark')
        } else {
            document.documentElement.setAttribute('data-bs-theme', theme)
        }
    }
    setTheme(getPreferredTheme())
    const showActiveTheme = theme => {
        const activeThemeIcon = document.querySelector('.theme-icon-active use')
        const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
        const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')
    
        document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
            element.classList.remove('active')
        })
    
        btnToActive.classList.add('active')
        activeThemeIcon.setAttribute('href', svgOfActiveBtn)
    }
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (storedTheme !== 'light' || storedTheme !== 'dark') {
            setTheme(getPreferredTheme())
        }
    })
    window.addEventListener('DOMContentLoaded', () => {
        showActiveTheme(getPreferredTheme())
    
        document.querySelectorAll('[data-bs-theme-value]')
        .forEach(toggle => {
            toggle.addEventListener('click', () => {
                const theme = toggle.getAttribute('data-bs-theme-value')
                localStorage.setItem('theme', theme)
                setTheme(theme)
                showActiveTheme(theme)
            })
        })
    })
})()
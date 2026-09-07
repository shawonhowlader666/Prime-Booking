<!-- Exact 100% Author Template Setting Offcanvas Drawer with Visible 7-Color Palette -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_setting" aria-labelledby="offcanvas_setting">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Template Setting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="mb-4">
            <h6>Set Theme Color</h6>
            <ul class="choose-skin list-unstyled mb-0">
                <li data-theme="ValenciaRed"><div style="--hotelair-theme-color: #DE3641;"></div></li>
                <li data-theme="SunOrange"><div style="--hotelair-theme-color: #F7A614;"></div></li>
                <li data-theme="AppleGreen"><div style="--hotelair-theme-color: #5BC43A;"></div></li>
                <li data-theme="CeruleanBlue"><div style="--hotelair-theme-color: #00B8D6;"></div></li>
                <li data-theme="Mariner"><div style="--hotelair-theme-color: #0066FE;"></div></li>
                <li data-theme="PurpleHeart" class="active"><div style="--hotelair-theme-color: #6238B3;"></div></li>
                <li data-theme="FrenchRose"><div style="--hotelair-theme-color: #EB5393;"></div></li>
            </ul>
        </div>
        <div class="svg-stroke mb-4">
            <h6>Icon SVG Stroke</h6>
            <div class="btn-group w-100" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="stroke" id="Stroke_A" value="svgstroke-a" checked>
                <label class="btn btn-outline-primary" for="Stroke_A">Stroke 1</label>
              
                <input type="radio" class="btn-check" name="stroke" id="Stroke_B" value="svgstroke-b">
                <label class="btn btn-outline-primary" for="Stroke_B">Stroke 2</label>
              
                <input type="radio" class="btn-check" name="stroke" id="Stroke_C" value="svgstroke-c">
                <label class="btn btn-outline-primary" for="Stroke_C">Stroke 3</label>
            </div>
        </div>
        <div class="mb-4">
            <h6>Layout Section Light/Dark</h6>
            <ul class="list-unstyled d-flex flex-wrap gap-2">
                <li class="sidebar-toggle flex-fill">
                    <input type="checkbox" class="btn-check" id="Sidebar_dark">
                    <label class="btn btn-outline-primary w-100" for="Sidebar_dark">Sidebar</label>
                </li>
                <li class="header-toggle flex-fill">
                    <input type="checkbox" class="btn-check" id="Header_dark">
                    <label class="btn btn-outline-primary w-100" for="Header_dark">Header</label>
                </li>
            </ul>
        </div>
        <div class="mb-4">
            <h6>More Setting</h6>
            <div class="form-check form-switch monochrome-toggle mb-2">
                <input class="form-check-input fs-6" type="checkbox" role="switch" id="monochrome">
                <label class="form-check-label" for="monochrome">Monochrome Mode</label>
            </div>
            <div class="form-check form-switch radius-toggle mb-2">
                <input class="form-check-input fs-6" type="checkbox" role="switch" id="radius0">
                <label class="form-check-label" for="radius0">Border Radius none</label>
            </div>
            <div class="form-check form-switch svg-icon-color mb-2">
                <input class="form-check-input fs-6" type="checkbox" role="switch" id="IconColor">
                <label class="form-check-label" for="IconColor">Sidebar Icon color</label>
            </div>
            <div class="form-check form-switch cb-shadow mb-2">
                <input class="form-check-input fs-6" type="checkbox" role="switch" id="BoxShadow">
                <label class="form-check-label" for="BoxShadow">Card box shadow active</label>
            </div>
        </div>
        <div class="d-flex">
            <a href="{{ route('sync-hub') }}" class="btn w-100 me-1 py-2 btn-primary">Prime OTA Sync</a>
            <button type="button" class="btn w-100 ms-1 py-2 btn-dark" onclick="window.location.reload()">Reset Defaults</button>
        </div>
    </div>
</div>

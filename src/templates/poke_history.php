<div class="container-fluid poke">
    <div class="container flex justify-center">
        <div class="poke-history">
            <h3 class="center-align">Poke istorija</h3>
            <div class="search-form flex justify-center">
                <div class="form-group search-wrapper">
                    <input type="text" id="search" placeholder="Ieškoti pagal vardą">
                </div>
                <div class="form-group datepicker-wrapper">
                    <input type="text" id="date_from" placeholder="Data nuo">
                </div>
                <div class="form-group datepicker-wrapper">
                    <input type="text" id="date_to" placeholder="Data iki">
                </div>
            </div>
            <div id="loader" class="loader-container" style="display: none;">
                <div class="loader"></div>
            </div>
            <div id="pokes-container">
            </div>
            <ul class="pagination center-align" id="pagination">
            </ul>
        </div>
    </div>
</div>
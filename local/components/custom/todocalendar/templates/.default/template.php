<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CJSCore::Init(['jquery2']);
$monthDate = "{$arResult['current']['mon']}.{$arResult['current']['year']}";
?>

<section>

    <div class="nav">
        <a href="<?=$arResult['uri']?>?timestamp=<?=$arResult['prev'][0]?>">
            < <?=$arResult['prev']['month']?> <?=$arResult['prev']['year']?>
        </a>

        <div>
            <?=$arResult['current']['month']?> <?=$arResult['current']['year']?>
        </div>

        <a href="<?=$arResult['uri']?>?timestamp=<?=$arResult['next'][0]?>">
            <?=$arResult['next']['month']?> <?=$arResult['next']['year']?> >
        </a>
    </div>

    <div class="container">
        <div class="days">
            <?foreach (['Пн', 'Вт', 'Ср','Чт', 'Пт', 'Сб', 'Вс'] as $weekDay) {?>
                <div class="week-day"><?=$weekDay?></div>
            <?}?>
            
            <?for ($i = 0; $i < $arResult['month_start_weekday']; $i++) {?>
                <div></div>
            <?}?>

            <?foreach (range(1, $arResult['days']) as $dayNum) {?>
                <div class="day" data-date="<?="$dayNum.$monthDate"?>"><?=$dayNum?></div>
            <?}?>
        </div>

        <div class="sidebar">
            <h3>Дела за <span id="sidebar-date"></span></h3>
            <div id="notes"></div>
            <div id="add-note">
                <form id="add-note-form" style="text-align: center;">
                    <input type="text" name="date" hidden id="date-input">
                    <textarea name="note" cols="24" rows="3"></textarea>
                    <button type="submit">Добавить дело</button>
                </form>
            </div>
        </div>
    </div>

</section>

<script>
    const signedParameters =  '<?= $this->getComponent()->getSignedParameters() ?>';
    const componentName = '<?= $this->getComponent()->getName() ?>';
    let notes = <?=\CUtil::PhpToJSObject($arResult['notes'])?>;

    const sidebar = $('.sidebar')
    const notesContainer = $('#notes')
    const form = $('#add-note-form')

    let selectedDay = null
        
    $('.day').click((e) => {
        if (selectedDay) {
            closeSidebar()
        }

        if (selectedDay !== e.target) {
            selectedDay = e.target;
            openSidebar()
        } else {
            selectedDay = null
        }

        e.preventDefault()
    })

    
    form.submit(function(e){
        e.preventDefault();
        BX.ajax.runComponentAction(componentName, 'addNote', {
            signedParameters: signedParameters,
            data: form.serialize()
        }).then(function (response) {
            const { dateNotes, date } = response.data;
            notes[date] = dateNotes;
            renderNotes();
        });
    });

    function renderNotes() {
        notesContainer.empty()
        const date = $(selectedDay).data('date')
        
        notes[date]?.forEach(note => {
            $('<div>')
                .addClass('note')
                .text(note)
                .appendTo(notesContainer)
        });
    }

    function openSidebar() {
        $(selectedDay).addClass('active')
        sidebar.addClass('open')

        const date = $(selectedDay).data('date')
        
        $('#sidebar-date').text(date)
        $('#date-input').val(date)
        renderNotes()
    }

    function closeSidebar() {
        $(selectedDay).removeClass('active')
        sidebar.removeClass('open')
    }
</script>

<style>
    .nav {
        display: flex;
        justify-content: space-evenly;
    }

    .container {
        overflow: hidden;
        display: flex;
        margin-top: 50px;
    }

    .sidebar {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;

        padding: 4px;
        border: #97b4ff solid 1px;
        border-radius: 4px;

        width: 0;
        translate: 300px 0;
        transition: cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s all;
    }

    .sidebar h3 {
        width: 100%;
        margin: 0;
        padding: 5px;
        text-align: center;
        border-bottom: solid 1px;
        border-color: inherit;
    }

    .sidebar.open {
        width: 300px;
        translate: 0;
    }
    
    /* days grid */
    .days {
        display: grid;
        height: fit-content;
        width: 100%;
        grid-template-columns: repeat(7, 35px);
        gap: 8px;
        justify-content: center;
        text-align: center;
    }

    /* days grid items */
    .day {
        cursor: pointer;
        padding: 8px;
        border-radius: 5px;
        border-bottom: #97b4ff solid 2px;
        border-right: #97b4ff solid 2px;
        border-top: #ddd solid 1px;
        border-left: #ddd solid 1px;
    }

    .day:nth-child(7n+6), .day:nth-child(7n+7) {
        color: #00c100;
    }

    .day.active {
        background-color: #e7e7e7;
    }

    .day:hover {
        background-color: #ddd;
    }

    .week-day {
        margin-bottom: 3px;
        color: #bbb;
    }

    /* notes */
    #notes {
        width: 100%;
        height: 100%;
        max-height: 200px;
        overflow-y: auto;
        margin: 3px 0;
    }
    
    .note {
        padding: 4px;
        border: #97b4ff solid 1px;
        border-radius: 4px;
        margin: 3px;
    }
</style>


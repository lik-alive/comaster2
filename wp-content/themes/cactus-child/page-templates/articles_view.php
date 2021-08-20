<?php
	/*
		Template Name: Articles View
	*/
	get_header(); 
	wp_enqueue_script('articles_view', get_template_directory_uri().'/js/articles_view.js');
	wp_enqueue_script('file-manager', get_template_directory_uri().'/js/tools/file-manager.js');
?>

<div class='main-container flex-container'>
	<div class='main-central'>
		<div class='panel'>
			<div class='flex-container' >			
				<div class='flex-float' >
					<div class='panel-title'>
						<div>
							<label>ID:</label> 
							<label name='ID_Article'></label>
						</div>
						<label name='Title'></label>
					</div>
					<div class='panel-subtitle'>
						<label name='Authors'/>
					</div>
				</div>
				<div id='infoStatus' class='panel-status' style='min-width:120px' >
					<label name='ITitle'/>
				</div>
			</div>
			
			
			<br/>
			
			<button type="button" class="btn btn-info collapser" data-toggle="collapse" data-target="#collapse1">
				Дополнительная информация
			</button>
			<div id='collapse1' class='collapse'>
				<div class='form-row' >
					<div class='form-name'>Секция:</div>
					<div class='form-value'><label name='STitle'/></div>
				</div>
				<div class='form-row' >
					<div class='form-name'>Аффилиация:</div>
					<div class='form-value'><label name='Affiliation'/></div>
				</div>
				<div id='maininfo'>
					<div class='form-row' >
						<div class='form-name'>Дата получения:</div>
						<div class='form-value'><label name='RecvDate'/></div>
					</div>
				</div>
				<div class='form-row' >
					<div class='form-name'>Число страниц:</div>
					<div class='form-value'><label name='PageCount'/></div>
				</div>
				<div class='form-row' >
					<div class='form-name'>Напоминание:</div>
					<div class='form-value'><label name='RemDate'/></div>
				</div>
				<div class='form-row' >
					<div class='form-name'>Вердикт редактора:</div>
					<div class='form-value'><label name='FinalVerdictDate'/></div>
				</div>
				<div class='form-row' >
					<div class='form-name'>Приоритет:</div>
					<div class='form-value'><label name='Priority'/></div>
				</div>
				<div class='form-row' >
					<div class='form-name'>Контакт:</div>
					<div class='form-value'><label name='CorAuthor'/></div>
				</div>
			</div>
			
			<button id='pdfbutton' type="button" class="btn btn-info collapser" style='display:none' data-toggle="collapse" data-target="#collapse2">
				Текст статьи
			</button>
			<div id='collapse2' class='collapse'>
				<object id='pdffile' data="" type="application/pdf" style='width: 100%; height: 800px'>
				</object>
			</div>
		</div>
		
		<?php if (g_cua('administrator')) { ?>
			<div class='panel'>
				<div class='flex-container' >
					<div class='panel-title flex-float'>
						<label>Прохождение статьи</label>
					</div>
				</div>
				
				<div class='scrollonoverflow'>
					<div id='statusListSci' class='status'></div>
					<div id='statusListTech' class='status'></div>
				</div>
				<br/>
				<?php if (g_cua('administrator')) { ?>
					<div class='flex-container'>
						<div class='flex-float'>
							<button id='massaction' type='button' class='btn btn-info' style='width:150px' disabled >
								<span class="glyphicon glyphicon-bullhorn"></span> Выслать все
							</button>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		
		<div class='panel'>
			<div class='flex-container' >
				<div class='panel-title flex-float'>
					<label>Научное рецензирование</label>
				</div>
				<div id='sciAppStatus' class='panel-status' style='min-width:120px' >
					<label name='SATitle'/>
				</div>
			</div>
			
			<div class='scrollonoverflow'>
				<table id='scitable' class='mydataTable'  >
					<thead>
						<tr>
							<th style='min-width: 30px; width: 30px'>№</th>
							<th>IDR</th>
							<th style='min-width:120px; width:120px;'>Рецензент</th>
							<th style='min-width:100px; width:100px;'>Послано</th>
							<th style='min-width:100px; width:100px;'>Ответ</th>
							<th style='min-width:80px; width:80px;'>Вердикт</th>
							<th style='min-width:100px; width:100px;'>Авторам</th>
							<th style='min-width:100px; width:100px;'>Ответ</th>
							<th style='min-width:100px; width:100px;'>Файлы</th>
							<th style='min-width:50px; width:50px;'>Оценка</th>
							<th style='min-width:100px; width:100px;'>Напом.</th>
						</tr>
					</thead>										
				</table>
			</div>
			<br/>
			
			<button type="button" class="btn btn-info collapser" data-toggle="collapse" data-target="#collapse3">
				Назначить рецензента
			</button>
			<div id='collapse3' class='collapse'>
				<div class='form-row' >
					<div class='form-name' >
						<label for='Search' style='font-weight:bold'>Умный поиск:</label>
					</div>
					<div class='form-value' >
						<input id='revsearch' type='text' class='long' name='Expert' value='' placeholder='Введите ФИО или ключевое слово...' autocomplete='off' />
					</div>
				</div>
				
				<table id='addrevtable' class='mydataTable' >
					<thead>
						<tr>
							<th style='min-width: 30px; width: 30px'>№</th>
							<th>IDR</th>
							<th>Выберите рецензента</th>
						</tr>
					</thead>										
				</table>
				<br/>
				<div class='flex-container'>
					<div class='flex-float'>
						<button id='addnewexpert' type="button" class="btn btn-warning" style='width:150px'>
							<span class="glyphicon glyphicon-plus"></span> Новый эксперт
						</button>
					</div>
					<div>
						<button  id='addreviewer_ok' type="button" class="btn btn-success" disabled style='width:150px'>
							<span class="glyphicon glyphicon-ok"></span> Назначить
						</button>
					</div>
				</div>
			</div>
		</div>
		
		<div class='panel'>
			<div class='flex-container' >
				<div class='panel-title flex-float'>
					<label>Техническое рецензирование</label>
				</div>
				<div id='techAppStatus' class='panel-status' style='min-width:120px' >
					<label name='TATitle'/>
				</div>
			</div>
			
			<div class='scrollonoverflow'>
				<table id='techtable' class='mydataTable' >
					<thead>
						<tr>
							<th style='min-width: 30px; width: 30px'>№</th>
							<th>IDV</th>
							<th style='min-width:100px; width:100px;'>Получено</th>
							<th style='min-width:100px; width:100px;'>Претензии</th>
							<th style='min-width:100px; width:100px;'>Авторам</th>
							<th style='min-width:100px; width:100px;'>Ответ</th>
							<th style='min-width:100px; width:100px;'>Файл</th>
						</tr>
					</thead>										
				</table>
			</div>
		</div>
	</div>
	
	<?php if (g_cua('administrator', 'jeditor', 'jteditor', 'jtech')) { ?>
		<div class='main-widgets'>
			<div class='sticky'>
				<?php if (g_cua('administrator', 'jeditor')) { ?>
					<div class='panel'>
						<div class='widget-title' ><label>Действия</label></div>
						<?php if (g_cua('administrator')) { ?>
							<button id='editaction' type="button" class="btn btn-primary action" style='width:100%'>
								<span class="glyphicon glyphicon-edit"></span> Редактировать
							</button>
							<button id='createaction' type="button" class="btn btn-warning action" style='width:100%'>
								<span class="glyphicon glyphicon-plus-sign"></span> Добавить статью
							</button>
						<?php } ?>
						<?php if (g_cua('administrator', 'jeditor')) { ?>
							<button id='sciappaction' type="button" class="btn btn-primary action" style='width:100%; display:none' >
								<span class="glyphicon glyphicon-ok"></span> Принять научно
							</button>
						<?php } ?>
						<?php if (g_cua('administrator', 'jeditor')) { ?>
							<button id='rejectaction' type="button" class="btn btn-danger action" style='width:100%'>
								<span class="glyphicon glyphicon-remove"></span> Отклонить
							</button>
						<?php } ?>
						<?php if (g_cua('administrator', 'jeditor')) { ?>
							<button id='reserveaction' type="button" class="btn btn-primary action" style='width:100%' >
								<span class="glyphicon glyphicon-book"></span> Назначить в выпуск
							</button>
						<?php } ?>
					</div>
				<?php } ?>
				
				<?php if (g_cua('administrator')) { ?>
					<div class='panel'>
						<div class='widget-title' ><label>Письма</label></div>
						<button id='remindaction' type="button" class="btn btn-danger action" style='width:100%'>
							<span class="glyphicon glyphicon-envelope"></span> Напомнить автору
						</button>
						<button id='camerareadyaction' type="button" class="btn btn-info action" style='width:100%'>
							<span class="glyphicon glyphicon-envelope"></span> Выслать гранки
						</button>
						<button id='letteraction' type="button" class="btn btn-info action" style='width:100%'>
							<span class="glyphicon glyphicon-envelope"></span> Новое письмо
						</button>
					</div>
				<?php } ?>
				
				<?php include 'tools/widget-chat.php'; ?>
				<?php include 'tools/widget-logs.php'; ?>
			</div>
		</div>
	<?php } ?>
</div>

<?php include 'tools/confirm-dialog.php'; ?>

<div id='fromExpertDialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center' >Ответ рецензента</h4>
		</div>
		<div class='modal-body'>
			<form id='fromExpertForm'>
				<fieldset>
					<legend>Краткая информация</legend>
					<div class='form-row'>
						<div class='form-name'>
							<label for='FromExpDate'><span class='required'>*</span>Дата получения:</label>
						</div>
						<div class='form-value'>
							<input type='date' name='FromExpDate' value='' required />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row'>
						<div class='form-name'>
							<label for='ID_Verdict'><span class='required'>*</span>Вердикт:</label>
						</div>
						<div class='form-value'>
							<select name='ID_Verdict' required >
								<?php
									$verdicts = tables_get_verdicts();
									echo "<option value=''>---</option>";
									foreach ($verdicts as $verdict){
										if ($verdict->ID_Verdict >= 5) 
										echo "<option value='{$verdict->ID_Verdict}'>---{$verdict->Title}---</option>";
										else
										echo "<option value='{$verdict->ID_Verdict}'>{$verdict->Title}</option>";
									}
								?>
							</select>
							<div style='margin-top: 4px'>
								<button class="btn btn-primary verdictpicker verd1" style='width:100px;' type='button' >
									добро
								</button>
								<button class="btn btn-info verdictpicker verd2" style='width:100px' type='button' >
									подправить
								</button>
								<button class="btn btn-warning verdictpicker verd3" style='width:100px' type='button' >
									переделать
								</button>
								<button class="btn btn-danger verdictpicker verd4" style='width:100px' type='button' >
									отклонить
								</button>
								<button class="btn btn-secondary verdictpicker verd5" style='width:100px' type='button' >
									отказался
								</button>
								<button class="btn btn-secondary verdictpicker verd6" style='width:100px' type='button' >
									снят
								</button>
							</div>
						</div>
					</div>
					<div id='reviewQuality' class='form-row'>
						<div class='form-name'>
							<label for='Quality'>Качество рецензии:</label>
						</div>
						<div class='form-value'>
							<select name='Quality'>
								<option value='1'>отписка</option>
								<option value='2' selected>хорошо</option>
								<option value='3'>превосходно</option>
							</select>
							<div style='margin-top: 4px'>
								<button class="btn btn-primary qualitypicker verd3" style='width:100px;' type='button' >
									превосходно
								</button>
								<button class="btn btn-info qualitypicker verd2" style='width:100px' type='button' >
									хорошо
								</button>
								<button class="btn btn-warning qualitypicker verd1" style='width:100px' type='button' >
									отписка
								</button>
							</div>
						</div>
					</div>
					<div id='confLetter' class='form-row'>
						<div class='form-name'>
							<label for='SendConfLetter'>Письмо подтверждения:</label>
						</div>
						<div class='form-value'>
							<input type='hidden' name='SendConfLetter' value='N' />
							<input type='checkbox' name='SendConfLetter' value='Y' checked />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Файлы</legend>
					<div class='form-row' >
						<div class='form-lefthalf' >
							<label for='pdfRevFile'><span id='pdfRevFileRequired' class='required'>*</span>Рецензия (*.pdf):</label>
							<div id='pdfRevFile'></div>
						</div>
						<div class='form-righthalf' >
							<label for='extraRevFile'>Экстра-материалы (*.pdf):</label>
							<div id='extraRevFile'></div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class='modal-footer'>													
			<button class="btn btn-success" style='width:120px' type='submit' form='fromExpertForm' >
				<span class="glyphicon glyphicon-ok"></span> Сохранить
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal' >
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>

<div id='fromAuthorDialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center' >Ответ автора</h4>
		</div>
		<div class='modal-body'>
			<form id='fromAuthorForm'>
				<fieldset>
					<legend>Краткая информация</legend>
					<div class='form-row'>
						<div class='form-name'>
							<label for='FromAuthDate'><span class='required'>*</span>Дата получения:</label>
						</div>
						<div class='form-value'>
							<input type='date' name='FromAuthDate' value='' required />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row'>
						<div class='form-name'>
							<label for='SendExpLetter'>Рецензенту:</label>
						</div>
						<div class='form-value'>
							<input type='hidden' name='SendExpLetter' value='N' />
							<input type='checkbox' name='SendExpLetter' value='Y' checked />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Файлы</legend>
					<div class='form-row'>
						<div class='form-name'>
							<label for='NoRepPDF'>Нет ответа:</label>
						</div>
						<div class='form-value'>
							<input type='hidden' name='NoRepPDF' value='N' />
							<input id='noRepPDF' type='checkbox' name='NoRepPDF' value='Y' />
						</div>
					</div>
					<div id='pdfRepFileDiv' class='form-row'>
						<div class='form-name' >
							<label for='pdfRepFile'><span class='required'>*</span>Ответ рецензенту (*.pdf):</label>
						</div>
						<div class='form-value' >
							<div id='pdfRepFile'></div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class='modal-footer'>													
			<button class="btn btn-success" style='width:120px' type='submit' form='fromAuthorForm' >
				<span class="glyphicon glyphicon-ok"></span> Сохранить
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal' >
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>

<div id='techComDialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center' >Технические замечания</h4>
		</div>
		<div class='modal-body'>
			<form id='techComForm'>
				<fieldset <?php if (!g_cua('administrator', 'jteditor')) echo "disabled='disabled'" ?> >
					<div class='form-row'>
						<div class='form-name'>
							<label for='Overall'><b>Переделать всё!!!</b></label>
						</div>
						<div class='form-value'>
							<input type='radio' name='Overall' value='0' required />
						</div>
					</div>
					<div class='form-row'>
						<div class='form-name'>
							<label for='Overall'><b>Претензий нет</b></label>
						</div>
						<div class='form-value'>
							<input type='radio' name='Overall' value='1' required />
						</div>
					</div>
					<div class='form-row'>
						<div class='form-name'>
							<label for='Overall'><b>Новые:</b></label>
						</div>
						<div class='form-value'>
							<input type='radio' name='Overall' value='2' required checked />
						</div>
					</div>
					<div id='techcomments'>
						<br/>
						<button id='prevcom' class="btn btn-info" style='width:230px' type='button' >
							См. предыдущую итерацию
						</button>
						<br/>
						<div class='form-row'>
							<div class='form-name'>
								<label for='WrongSubject'>Нет темы:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='WrongSubject' value='N' />
								<input type='checkbox' name='WrongSubject' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='NeedVector'>Нужен вектор:</label>
							</div>
							<div class='form-value'>
								<input type='text' class='long' name='NeedVector' value='' placeholder='Только номера рисунков (1, 3, 5...)' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='SeparateFiles'>Отдельными файлами:</label>
							</div>
							<div class='form-value'>
								<input type='text' class='long' name='SeparateFiles' value='' placeholder='Только номера рисунков (1, 3, 5...)' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='BlackPictures'>Чёрные рисунки:</label>
							</div>
							<div class='form-value'>
								<input type='text' class='long' name='BlackPictures' value='' placeholder='Только номера рисунков (1, 3, 5...)' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='WrongLinking'>Неверная линковка:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='WrongLinking' value='N' />
								<input type='checkbox' name='WrongLinking' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='AutoNumbering'>Автонумерация:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='AutoNumbering' value='N' />
								<input type='checkbox' name='AutoNumbering' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='MSEquation'>MS Equation:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='MSEquation' value='N' />
								<input type='checkbox' name='MSEquation' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='ColorPictures'>Цветные рисунки:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='ColorPictures' value='N' />
								<input type='checkbox' name='ColorPictures' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='PictureTables'>Таблица рисунком:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='PictureTables' value='N' />
								<input type='checkbox' name='PictureTables' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='Fractions'>Точки в дробях:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='Fractions' value='N' />
								<input type='checkbox' name='Fractions' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='NoGRNTI'>Отсутствует ГРНТИ:</label>
							</div>
							<div class='form-value'>
								<input type='hidden' name='NoGRNTI' value='N' />
								<input type='checkbox' name='NoGRNTI' value='Y' />
							</div>
						</div>
						<div class='form-row'>
							<div class='form-name'>
								<label for='Others'>Прочее:</label>
							</div>
							<div class='form-value'>
								<textarea name='Others' rows='5' placeholder='Заполните каждое замечание с новой строки (нумеровать не нужно)'></textarea>
							</div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class='modal-footer'>			
			<?php if (g_cua('administrator', 'jteditor')) { ?>
				<button class="btn btn-success" style='width:120px' type='submit' form='techComForm' >
					<span class="glyphicon glyphicon-ok"></span> Сохранить
				</button>
			<?php } ?>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal' >
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>

<div id='newVersionDialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center' >Новая версия статьи</h4>
		</div>
		<div class='modal-body'>
			<form id='newVersionForm'>
				<fieldset>
					<legend>Краткая информация</legend>
					<div class='form-row'>
						<div class='form-name'>
							<label for='RecvDate'><span class='required'>*</span>Дата получения:</label>
						</div>
						<div class='form-value'>
							<input type='date' name='RecvDate' value='' required />
							<?php include 'tools/daypicker.php'; ?>
						</div>
					</div>
					<div class='form-row'>
						<div class='form-name'>
							<label for='SendConfLetter'>Письмо подтверждения:</label>
						</div>
						<div class='form-value'>
							<input type='hidden' name='SendConfLetter' value='N' />
							<input type='checkbox' name='SendConfLetter' value='Y' checked />
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Файлы</legend>
					<div class='form-row'>
						<div class='form-name'>
							<label for='NoUpdatePDF'>Не обновлять PDF:</label>
						</div>
						<div class='form-value'>
							<input type='hidden' name='NoUpdatePDF' value='N' />
							<input id='noUpdatePDF' type='checkbox' name='NoUpdatePDF' value='Y' />
						</div>
					</div>
					<div id='pdfVerFileDiv' class='form-row'>
						<div class='form-name'>
							<label for='pdfVerFile'><span class='required'>*</span>Статья (*.pdf):</label>
						</div>
						<div class='form-value' >
							<div id='pdfVerFile'></div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class='modal-footer'>													
			<button class="btn btn-success" style='width:120px' type='submit' form='newVersionForm' >
				<span class="glyphicon glyphicon-ok"></span> Сохранить
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal' >
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>

<div id='reserveDialog' class='modal'>
	<div class='modal-content' style='width:600px'>
		<div class='modal-header'>
			<h4 class='modal-title' style='text-align: center' >Назначить в выпуск</h4>
		</div>
		<div class='modal-body'>
			<form id='reserveForm'>
				<fieldset>
					<legend>Краткая информация</legend>
					<div class='form-row' >
						<div class='form-name' >
							<label for='ID_Issue'><span class='required'>*</span>Выпуск:</label>
						</div>
						<div class='form-value' >
							<select id='issues' name='ID_Issue' required >
								<?php
									$issues = tables_get_issues_active();
									foreach ($issues as $issue){
										echo "<option value='{$issue->ID_Issue}'>{$issue->Title}</option>";
									}
								?>
							</select>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class='modal-footer'>													
			<button class="btn btn-success" style='width:120px' type='submit' form='reserveForm' >
				<span class="glyphicon glyphicon-ok"></span> Сохранить
			</button>
			<button class="btn btn-secondary" style='width:120px' type='button' data-dismiss='modal' >
				<span class="glyphicon glyphicon-remove"></span> Отмена
			</button>
		</div>
	</div>
</div>


<?php include 'tools/letters.php'; ?>

<?php get_footer(); ?>

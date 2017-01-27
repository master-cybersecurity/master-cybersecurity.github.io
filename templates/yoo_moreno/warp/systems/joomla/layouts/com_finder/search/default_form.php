<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

?>

<script type="text/javascript">
	jQuery(function($) {
<?php if ($this->params->get('show_advanced', 1)): ?>
		/*
		 * This segment of code adds the slide effect to the advanced search box.
		 */
		<?php if (!$this->params->get('expand_advanced', 0)): ?>
		$('#advanced-search').hide();
		<?php endif; ?>

		$('#advanced-search-toggle').on('click', function(e) {
			e.preventDefault();
			$('#advanced-search').slideToggle();
		});

		/*
		 * This segment of code disables select boxes that have no value when the
		 * form is submitted so that the URL doesn't get blown up with null values.
		 */
		$('#finder-search').on('submit', function(){

			// Disable select boxes with no value selected.
			$('#advanced-search select').each(function(s){

				var s = $(this);

				if (!s.val()) {
					s.attr('disabled', 'disabled');
				}

			});
		});

<?php endif; ?>
		/*
		 * This segment of code sets up the autocompleter.
		 */
<?php if ($this->params->get('show_autosuggest', 1)): ?>
	<?php JHtml::script('com_finder/autocompleter.js', false, true); ?>
	var url = '<?php echo JRoute::_('index.php?option=com_finder&task=suggestions.display&format=json&tmpl=component', false); ?>';
	var completer = new Autocompleter.Request.JSON(document.id('q'), url, {'postVar': 'q'});
<?php endif; ?>
	});
</script>

<form class="uk-form uk-panel uk-panel-box" id="finder-search" action="<?php echo JRoute::_($this->query->toURI()); ?>" method="get">

	<?php echo $this->getFields(); ?>

	<?php if (false && $this->state->get('list.ordering') !== 'relevance_dsc') : ?>
		<input type="hidden" name="o" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" />
	<?php endif; ?>

	<fieldset>
		<legend><?php echo JText::_('COM_FINDER'); ?></legend>

		<div class="uk-form-row">
			<label for="q"><?php echo JText::_('COM_FINDER_SEARCH_TERMS'); ?></label>
			<input type="text" name="q" id="q" size="30" value="<?php echo $this->escape($this->query->input); ?>" />
		</div>

		<div class="uk-form-row">
			<?php if ($this->escape($this->query->input) != '' || $this->params->get('allow_empty_search')):?>
				<button class="uk-button uk-button-primary" name="Search" type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT');?></button>
			<?php else: ?>
				<button class="uk-button uk-button-primary" name="Search" type=""><?php echo JText::_('JSEARCH_FILTER_SUBMIT');?></button>
			<?php endif; ?>
		</div>

		<?php if ($this->params->get('show_advanced', 1)): ?>
		<div class="uk-form-row">
			<a id="advanced-search-toggle"><?php echo JText::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE'); ?></a>
			<div id="advanced-search">

				<?php if ($this->params->get('show_advanced_tips', 1)): ?>
				<div>
					<?php echo JText::_('COM_FINDER_ADVANCED_TIPS'); ?>
				</div>
				<?php endif; ?>

				<div id="finder-filter-window">
					<?php echo JHtml::_('filter.select', $this->query, $this->params); ?>
				</div>

			</div>
		</div>
		<?php endif; ?>

	</fieldset>

</form>
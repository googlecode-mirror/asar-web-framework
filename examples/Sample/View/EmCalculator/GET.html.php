<h1>Em Calculator</h1>
<p>
	Em Calculator is a tool to calculate em values given a base font-size.
	It can also suggest to you a line-height value in ems if you want. <br />
	<a href="<?=$this->getController()->getContext()->getPath()?>">Back to parent...</a>
</p>

<form action="<?$_SERVER['PHP_SELF']?>" method="post" accept-charset="utf-8" id="emcalc">
	<ul class="form">
		<li>
			<label for="emcalc_base-font-size">Base Font Size:</label>
			<input type="text" name="emcalc_base-font-size" value="" id="ecalc_base-font-size" />
		</li>
		<li>
			<label for="emcalc_base-line-height">Base Line Height:</label>
			<input type="text" name="emcalc_base-line-height" value="" id="emcalc_base-line-height" />
		</li>
		<li>
			<label for="emcalc_expected-font-size">Expected Font Size:</label>
			<input type="text" name="emcalc_expected-font-size" value="" id="emcalc_expected-font-size" />
		</li>
		<li class="actions">
			<input type="submit" name="emcalc_submit" value="Calculate" id="emcalc_submit" />
		</li>
	</ul>
	
</form>

<?if ($this['font-size']):?>
	<h2>Font Size: <?=$this['font-size']?>em</h2>
	<h2>Suggested Line-height: <?=$this['line-height']?>em</h2>
<?endif;?>
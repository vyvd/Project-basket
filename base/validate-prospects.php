<div class="container-fluid" id="validate-prospects">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-12">
				<h2 class="wow fadeInUp">Validate Qualification</h2>
				<p>Enter student's certificate details below:</p>
				<form name="validateQualification">
					<input type="text" placeholder="First Name" name="firstname">
					<input type="text" placeholder="Last Name" name="lastname">
					<input type="text" placeholder="Certification Number" name="number">
					<input type="submit" value="Check Validation">
				</form>
                <?php
                $this->renderFormAjax("course", "validate-qualification", "validateQualification");
                ?>
			</div>
			<div class="col-lg-6 col-12">
				<div id="free-guide">
					<h3 class="wow fadeInUp">Get a Free Guide to Improve your Career Prospects</h3>
					<p>Subscribe to our weekly newsletter and we will send you our latest news and offers, as well as free copy of our informative guide titled:<br/> 
					<strong>The Ultimate Guide to Career Intervention</strong></p>
					<form>
						<input type="text" placeholder="First Name">
						<input type="email" placeholder="Your Email">
						<input type="submit" value="Get Free Guide" class="wow pulse">
					</form>
					<p>Your information is important to us.<br/> We will not pass it on to third parties</p>
				</div>
			</div>
		</div>
	</div>
</div>
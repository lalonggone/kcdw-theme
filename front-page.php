<?php
/**
 * Template: Front Page
 *
 * Sections: hero, alert bar, issue cards, lawsuits, latest news, take action CTA.
 *
 * @package KCDW\Theme
 */

get_header();
?>

<!-- ================================================================
     HERO
     ================================================================ -->
<section class="site-hero">
	<div class="site-hero__inner">

		<p class="eyebrow">Moab, Utah — Colorado River Corridor</p>
		<h1>Stop Echo Canyon.</h1>
		<p class="hero__body">A developer is pushing to build a luxury resort on the Colorado River corridor — destroying floodplain, threatening water rights, and displacing the community. We are fighting to stop it.</p>

		<div class="hero__actions">
			<a class="btn btn--sienna" href="<?php echo esc_url( home_url( '/take-action/sign-the-petition/' ) ); ?>">Sign the Petition</a>
			<a class="btn btn--outline hero__secondary-cta" href="<?php echo esc_url( home_url( '/the-fight/' ) ); ?>">What We're Fighting</a>
		</div>

	</div>
</section>

<!-- ================================================================
     ALERT BAR — update with latest urgent development
     ================================================================ -->
<div class="alert-bar">
	<div class="alert-bar__inner">
		<p class="alert-bar__text">&#9888; Latest: Replace this with the most urgent current development — a court date, a hearing, a deadline. <a href="<?php echo esc_url( home_url( '/our-lawsuits/' ) ); ?>">See all lawsuits &#8594;</a></p>
	</div>
</div>

<!-- ================================================================
     THE FIGHT — issue cards
     ================================================================ -->
<section class="section section--issues">
	<div class="section__inner">

		<p class="section__eyebrow">The Fight</p>
		<h2>What We're Up Against</h2>

		<div class="issues-grid">

			<div class="issue-card">
				<h3>Water Rights</h3>
				<p>The resort would divert water from the Colorado River during a historic drought. We are challenging this legally.</p>
				<p class="card__readmore"><a href="<?php echo esc_url( home_url( '/the-fight/water-rights/' ) ); ?>">Read more &#8594;</a></p>
			</div>

			<div class="issue-card">
				<h3>Floodplain &amp; Flood Risk</h3>
				<p>The proposed site sits in an active floodplain. Building here puts residents, guests, and the river itself at risk.</p>
				<p class="card__readmore"><a href="<?php echo esc_url( home_url( '/the-fight/floodplain-flood-risk/' ) ); ?>">Read more &#8594;</a></p>
			</div>

			<div class="issue-card">
				<h3>Affordable Housing</h3>
				<p>Moab already has a housing crisis. This project doubles down on luxury while working families are priced out.</p>
				<p class="card__readmore"><a href="<?php echo esc_url( home_url( '/the-fight/affordable-housing/' ) ); ?>">Read more &#8594;</a></p>
			</div>

			<div class="issue-card">
				<h3>Cultural Resources</h3>
				<p>The site contains irreplaceable ancestral sites and rock art. Development would erase thousands of years of history.</p>
				<p class="card__readmore"><a href="<?php echo esc_url( home_url( '/the-fight/cultural-resources/' ) ); ?>">Read more &#8594;</a></p>
			</div>

		</div>
	</div>
</section>

<!-- ================================================================
     LAWSUITS
     ================================================================ -->
<section class="section section--lawsuits">
	<div class="section__inner">
		<div class="lawsuits-layout">

			<div class="lawsuits-layout__main">
				<p class="section__eyebrow">Our Lawsuits</p>
				<h2>We're Taking This to Court</h2>
				<p>KCDW has filed legal challenges targeting the water diversion permit and the state legislation that tried to clear the path for this development. Active litigation is ongoing.</p>
				<div class="section__actions">
					<a class="btn btn--sienna" href="<?php echo esc_url( home_url( '/our-lawsuits/' ) ); ?>">See Active Cases</a>
				</div>
			</div>

			<div class="lawsuits-layout__cards">
				<div class="lawsuit-card">
					<p class="lawsuit-card__status">Active</p>
					<h4>Water Rights Lawsuit</h4>
					<p>Challenging the diversion permit for the Colorado River.</p>
				</div>
				<div class="lawsuit-card">
					<p class="lawsuit-card__status">Active</p>
					<h4>SB 258 Challenge</h4>
					<p>Challenging the state legislation that fast-tracked approval.</p>
				</div>
			</div>

		</div>
	</div>
</section>

<!-- ================================================================
     LATEST NEWS — auto-populated from posts
     ================================================================ -->
<section class="section section--news">
	<div class="section__inner">

		<p class="section__eyebrow">In the News</p>
		<h2>Latest Updates</h2>

		<?php
		$kcdw_news = new WP_Query( [
			'posts_per_page' => 3,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );
		?>

		<?php if ( $kcdw_news->have_posts() ) : ?>
			<div class="news-grid">
				<?php while ( $kcdw_news->have_posts() ) : $kcdw_news->the_post(); ?>
					<?php get_template_part( 'parts/card', 'news', [ 'heading_level' => 'h3' ] ); ?>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p class="section--news__empty">No updates yet — check back soon.</p>
		<?php endif; ?>

		<div class="section__actions section__actions--center">
			<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/in-the-news/press-coverage/' ) ); ?>">All Press Coverage</a>
		</div>

	</div>
</section>

<!-- ================================================================
     TAKE ACTION
     ================================================================ -->
<section class="section section--cta">
	<div class="section__inner">

		<p class="section__eyebrow">Take Action</p>
		<h2>Every Action Counts</h2>

		<div class="action-grid">

			<div class="action-card">
				<h4>Sign the Petition</h4>
				<p>Add your name to the thousands demanding the county reject this proposal.</p>
				<p class="card__readmore"><a href="<?php echo esc_url( home_url( '/take-action/sign-the-petition/' ) ); ?>">Sign now &#8594;</a></p>
			</div>

			<div class="action-card">
				<h4>Show Up</h4>
				<p>Public hearings and county meetings are where this fight is won or lost. Be there.</p>
				<p class="card__readmore"><a href="<?php echo esc_url( home_url( '/take-action/show-up/' ) ); ?>">See dates &#8594;</a></p>
			</div>

			<div class="action-card">
				<h4>Contact Officials</h4>
				<p>Grand County commissioners need to hear from you. We make it easy.</p>
				<p class="card__readmore"><a href="<?php echo esc_url( home_url( '/take-action/contact-officials/' ) ); ?>">Send a message &#8594;</a></p>
			</div>

			<div class="action-card action-card--donate">
				<h4>Donate</h4>
				<p>Legal fights cost money. Your donation directly funds the attorneys keeping this in court.</p>
				<a class="btn btn--sienna" href="<?php echo esc_url( home_url( '/donate/' ) ); ?>">Donate Now</a>
			</div>

		</div>
	</div>
</section>

<?php get_footer(); ?>

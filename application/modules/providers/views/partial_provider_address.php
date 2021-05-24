<?php $this->load->helper('country'); ?>

<span class="provider-address-street-line">
    <?php echo($provider->provider_address_1 ? htmlsc($provider->provider_address_1) . '<br>' : ''); ?>
</span>
<span class="provider-address-street-line">
    <?php echo($provider->provider_address_2 ? htmlsc($provider->provider_address_2) . '<br>' : ''); ?>
</span>
<span class="provider-adress-town-line">
    <?php echo($provider->provider_city ? htmlsc($provider->provider_city) . ' ' : ''); ?>
    <?php echo($provider->provider_state ? htmlsc($provider->provider_state) . ' ' : ''); ?>
    <?php echo($provider->provider_zip ? htmlsc($provider->provider_zip) : ''); ?>
</span>
<span class="provider-adress-country-line">
    <?php echo($provider->provider_country ? '<br>' . get_country_name(trans('cldr'), $provider->provider_country) : ''); ?>
</span>

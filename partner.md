---
layout: partner
title: Partner
order: 5
---

{% for partner in site.data.partners %}
[![{{partner.name}}]({{ partner.img | prepend: "/logo/sponsor/" }}){:.img-responsive.center-block}]({{ partner.url }}){:.col-sm-3}{:target="_blank"}
{% endfor %}

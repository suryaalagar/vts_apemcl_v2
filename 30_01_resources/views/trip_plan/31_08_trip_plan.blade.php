@extends('layouts.app')
<div class="card-body">
    <p>Use <code>.nav-justified.nav-underline</code> classes justified bottom bordered tabs.</p>
    <ul class="nav nav-tabs nav-underline no-hover-bg nav-justified" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="active-tab32" data-toggle="tab" href="#active32" aria-controls="active32"
                role="tab" aria-selected="true">Active</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="link-tab32" data-toggle="tab" href="#link32" aria-controls="link32" role="tab"
                aria-selected="false">Link</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true">
                Dropdown
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" id="dropdownOpt1-tab2" href="#dropdownOpt21" data-toggle="tab"
                    aria-controls="dropdownOpt21" role="tab" aria-selected="true">dropdown 1</a>
                <a class="dropdown-item" id="dropdownOpt2-tab2" href="#dropdownOpt22" data-toggle="tab"
                    aria-controls="dropdownOpt22" role="tab" aria-selected="true">dropdown 2</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="linkOpt-tab2" data-toggle="tab" href="#linkOpt2" aria-controls="linkOpt2">Another
                Link</a>
        </li>
    </ul>
    <div class="tab-content px-1 pt-1">
        <div class="tab-pane active in" id="active32" aria-labelledby="active-tab32" role="tabpanel">
            <p>Macaroon candy canes tootsie roll wafer lemon drops liquorice jelly-o tootsie roll cake. Marzipan
                liquorice soufflé cotton candy jelly cake jelly-o sugar plum marshmallow. Dessert cotton candy macaroon
                chocolate sugar plum cake donut.</p>
        </div>
        <div class="tab-pane" id="link32" aria-labelledby="link-tab32" role="tabpanel">
            <p>Chocolate bar gummies sesame snaps. Liquorice cake sesame snaps cotton candy cake sweet brownie. Cotton
                candy candy canes brownie. Biscuit pudding sesame snaps pudding pudding sesame snaps biscuit tiramisu.
            </p>
        </div>
        <div class="tab-pane" id="dropdownOpt21" aria-labelledby="dropdownOpt1-tab2" role="tabpanel">
            <p>Fruitcake marshmallow donut wafer pastry chocolate topping cake. Powder powder gummi bears jelly beans.
                Gingerbread cake chocolate lollipop. Jelly oat cake pastry marshmallow sesame snaps.</p>
        </div>
        <div class="tab-pane" id="dropdownOpt22" aria-labelledby="dropdownOpt2-tab2" role="tabpanel">
            <p>Soufflé cake gingerbread apple pie sweet roll pudding. Sweet roll dragée topping cotton candy cake jelly
                beans. Pie lemon drops sweet pastry candy canes chocolate cake bear claw cotton candy wafer.</p>
        </div>
        <div class="tab-pane" id="linkOpt2" aria-labelledby="linkOpt-tab2" role="tabpanel">
            <p>Cookie icing tootsie roll cupcake jelly-o sesame snaps. Gummies cookie dragée cake jelly marzipan donut
                pie macaroon. Gingerbread powder chocolate cake icing. Cheesecake gummi bears ice cream marzipan.</p>
        </div>
    </div>
</div>

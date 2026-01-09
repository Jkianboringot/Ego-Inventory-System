<div class="sidebar-wrapper">
    <nav class="mt-2">
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

            {{-- Dashboard (everyone) --}}
            <x-new-nav-link title="Dashboard" bi_icon="bi-speedometer" route="admin.dashboard" />

            {{-- Admin Controls --}}
            @role('Supervisor')
            <x-new-nav-link-dropdown title="Admin Controls" bi_icon="bi-gear">
                <x-new-nav-link title="Roles" bi_icon="bi-people" route="admin.roles.index" />
                <x-new-nav-link title="Users" bi_icon="bi-clipboard2-check" route="admin.users.index" />
                <x-new-nav-link title="Approval Center" bi_icon="bi-person-badge" route="admin.approvals.approvalcenter" />
                <x-new-nav-link title="Defected Products" bi_icon="bi-bag-x" route="admin.defected.index" />
                <x-new-nav-link title="Activity Log" bi_icon="bi-clock-history" route="admin.activity-logs.index" />
            </x-new-nav-link-dropdown>
            @endrole

            {{-- CRM --}}
            @role(['Sales Clerk','Supervisor'])
            <x-new-nav-link-dropdown title="CRM" bi_icon="bi-people">
                <x-new-nav-link title="Customers" bi_icon="bi-truck-front" route="admin.customers.index" />

                @role(['Supervisor'])
                <x-new-nav-link title="Suppliers" bi_icon="bi-box" route="admin.suppliers.index" />
                @endrole
            </x-new-nav-link-dropdown>
            @endrole

            {{-- Products --}}
            @role(['Inventory Clerk','Supervisor'])
            <x-new-nav-link-dropdown title="Products" bi_icon="bi-box-seam">

                @role('Supervisor')
                <x-new-nav-link title="Units" bi_icon="bi-tags" route="admin.units.index" />
                <x-new-nav-link title="Brands" bi_icon="bi-grid-1x2" route="admin.brands.index" />
                <x-new-nav-link title="Categories" bi_icon="bi-bounding-box" route="admin.productcategories.index" />
                @endrole

                <x-new-nav-link title="Products" bi_icon="bi-boxes" route="admin.products.index" />

            </x-new-nav-link-dropdown>
            @endrole

            {{-- Inventory --}}
            @role(['Supervisor','Admin', 'Inventory Clerk', 'Sales Clerk', 'Warehouse Keeper', 'Return and Exchange Clerk'])
            <x-new-nav-link-dropdown title="Inventory" bi_icon="bi-cash-stack">

                @role(['Supervisor','Admin'])
                <x-new-nav-link-dropdown title="Purchases" bi_icon="bi-bag-check" route="admin.purchases*">
                    <x-new-nav-link title="Purchase List" route="admin.purchases.index" />
                    <x-new-nav-link title="Unsettled Purchases" route="admin.purchases.unsettled" />
                </x-new-nav-link-dropdown>
                @endrole

                @role(['Inventory Clerk','Supervisor','Admin'])
                <x-new-nav-link title="New Arrivals" bi_icon="bi-dropbox" route="admin.add-products.index" />
                @endrole

                @role(['Sales Clerk','Supervisor','Admin'])
                <x-new-nav-link title="Sales" bi_icon="bi-bar-chart" route="admin.sales.index" />
                @endrole

                @role(['Warehouse Keeper','Supervisor','Admin'])
                <x-new-nav-link title="Orders" bi_icon="bi-cart-check" route="admin.orders.index" />
                <x-new-nav-link title="Unsuccessful" bi_icon="bi-window-x" route="admin.unsuccessful-transactions.index" />
                @endrole

                @role(['Return and Exchange Clerk','Supervisor','Admin'])
                <x-new-nav-link title="Returns" bi_icon="bi-arrow-return-left" route="admin.returns.index" />
                @endrole

            </x-new-nav-link-dropdown>
            @endrole

        </ul>
    </nav>
</div>
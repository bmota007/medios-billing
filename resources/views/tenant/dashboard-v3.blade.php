@extends('layouts.dashboard-v3')

@section('content')

<div class="mbx-dashboard">

    <!-- KPI ROW -->
    <section class="mbx-kpi-row">

        <div class="mbx-kpi mbx-purple">
            <div class="mbx-kpi-icon">$</div>
            <div>
                <div class="mbx-kpi-label">Monthly Revenue</div>
                <div class="mbx-kpi-value">$24,920.00</div>
                <div class="mbx-kpi-growth">↑ 12.5% from last month</div>
            </div>
            <div class="mbx-spark spark-purple"></div>
        </div>

        <div class="mbx-kpi mbx-blue">
            <div class="mbx-kpi-icon">👥</div>
            <div>
                <div class="mbx-kpi-label">Active Customers</div>
                <div class="mbx-kpi-value">82</div>
                <div class="mbx-kpi-growth">↑ 7 this month</div>
            </div>
            <div class="mbx-spark spark-blue"></div>
        </div>

        <div class="mbx-kpi mbx-green">
            <div class="mbx-kpi-icon">📄</div>
            <div>
                <div class="mbx-kpi-label">Open Quotes</div>
                <div class="mbx-kpi-value">14</div>
                <div class="mbx-kpi-growth">↑ 4 new this week</div>
            </div>
            <div class="mbx-spark spark-green"></div>
        </div>

        <div class="mbx-kpi mbx-orange">
            <div class="mbx-kpi-icon">🛡</div>
            <div>
                <div class="mbx-kpi-label">Payment Health</div>
                <div class="mbx-kpi-value">97%</div>
                <div class="mbx-kpi-growth">Healthy</div>
            </div>
            <div class="mbx-spark spark-orange"></div>
        </div>

        <div class="mbx-kpi mbx-cyan">
            <div class="mbx-kpi-icon">💰</div>
            <div>
                <div class="mbx-kpi-label">Total Revenue</div>
                <div class="mbx-kpi-value">$248,540</div>
                <div class="mbx-kpi-growth">↑ 18.3% from last month</div>
            </div>
            <div class="mbx-spark spark-cyan"></div>
        </div>

    </section>

    <!-- MAIN PREMIUM GRID -->
    <section class="mbx-main-grid">

        <!-- LEFT BIG CHART -->
        <div class="mbx-card mbx-revenue-card">
            <div class="mbx-card-head">
                <div>
                    <h3>Revenue Overview</h3>
                    <p>This Month</p>
                </div>
                <span class="mbx-pill">Live</span>
            </div>

            <div class="mbx-chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="mbx-chart-stats">
                <div>
                    <strong>$24,920.00</strong>
                    <span>This Month</span>
                </div>
                <div>
                    <strong>$22,180.00</strong>
                    <span>Last Month</span>
                </div>
                <div>
                    <strong class="green-text">↑ 12.5%</strong>
                    <span>Growth</span>
                </div>
            </div>
        </div>

        <!-- CENTER RECENT -->
        <div class="mbx-card mbx-recent-card">
            <div class="mbx-card-head">
                <h3>Recent Invoices</h3>
                <a href="{{ route('invoice.history') }}">View All</a>
            </div>

            <div class="mbx-invoice-list">
                <a href="{{ route('invoice.history') }}" class="mbx-invoice">
                    <div>
                        <strong>INV-2026-0424</strong>
                        <span>Acme Corporation</span>
                    </div>
                    <div>
                        <b>$1,250.00</b>
                        <em class="paid">Paid</em>
                    </div>
                </a>

                <a href="{{ route('invoice.history') }}" class="mbx-invoice">
                    <div>
                        <strong>INV-2026-0423</strong>
                        <span>Globex Industries</span>
                    </div>
                    <div>
                        <b>$2,850.00</b>
                        <em class="pending">Pending</em>
                    </div>
                </a>

                <a href="{{ route('invoice.history') }}" class="mbx-invoice">
                    <div>
                        <strong>INV-2026-0422</strong>
                        <span>Stark Industries</span>
                    </div>
                    <div>
                        <b>$950.00</b>
                        <em class="paid">Paid</em>
                    </div>
                </a>

                <a href="{{ route('invoice.history') }}" class="mbx-invoice">
                    <div>
                        <strong>INV-2026-0421</strong>
                        <span>Wayne Enterprises</span>
                    </div>
                    <div>
                        <b>$1,500.00</b>
                        <em class="pending">Pending</em>
                    </div>
                </a>
            </div>
        </div>

        <!-- RIGHT 4 CARDS -->
        <div class="mbx-right-grid">

            <div class="mbx-card">
                <div class="mbx-card-head">
                    <h3>Quick Actions</h3>
                </div>

                <div class="mbx-action-grid">
                    <a href="{{ route('invoice.create') }}">🧾<span>Create Invoice</span></a>
                    <a href="{{ route('quotes.create') }}">📄<span>New Quote</span></a>
                    <a href="{{ route('customers.create') }}">👤<span>Add Customer</span></a>
                    <a href="{{ route('customers.index') }}">👥<span>Customers</span></a>
                    <a href="{{ route('invoice.history') }}">📊<span>Reports</span></a>
                    <a href="{{ route('quotes.index') }}">📤<span>Quotes</span></a>
                </div>
            </div>

            <div class="mbx-card">
                <div class="mbx-card-head">
                    <h3>Customer Status</h3>
                    <span>Live Mix</span>
                </div>

                <div class="mbx-donut">
                    <div class="mbx-donut-ring">
                        <strong>82</strong>
                        <span>Total</span>
                    </div>
                </div>

                <div class="mbx-status-list">
                    <div><span class="dot green"></span>Active <b>68</b></div>
                    <div><span class="dot blue"></span>New Leads <b>9</b></div>
                    <div><span class="dot orange"></span>Need Follow Up <b>5</b></div>
                </div>
            </div>

            <div class="mbx-card">
                <div class="mbx-card-head">
                    <h3>Notifications</h3>
                    <a href="#">View All</a>
                </div>

                <div class="mbx-notes">
                    <div><span>💵</span><p>Payment received from Acme Corporation</p><small>2 min ago</small></div>
                    <div><span>📄</span><p>Quote approved by homeowner</p><small>1 hour ago</small></div>
                    <div><span>👤</span><p>New customer added</p><small>3 hours ago</small></div>
                </div>
            </div>

            <div class="mbx-card">
                <div class="mbx-card-head">
                    <h3>Stripe Health Status</h3>
                    <span>Live Status</span>
                </div>

                <div class="mbx-health-list">
                    <div><span>API Connection</span><b>Healthy</b></div>
                    <div><span>Webhook</span><b>Connected</b></div>
                    <div><span>Last Payment</span><b>2 min ago</b></div>
                    <div><span>Status</span><b>Operational</b></div>
                </div>

                <a href="#" class="mbx-card-link">View Stripe Dashboard →</a>
            </div>

        </div>

    </section>

</div>

@endsection

<?php

namespace App\Http\Controllers;

use App\Models\InvestmentProduct;
use App\Models\InvestmentPortfolio;
use App\Models\InvestmentTransaction;
use App\Services\InvestmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    protected $investmentService;

    public function __construct(InvestmentService $investmentService)
    {
        $this->investmentService = $investmentService;
    }

    /**
     * Display investment dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $portfolioSummary = $this->investmentService->getPortfolioSummary($user);
        $availableProducts = InvestmentProduct::where('status', 'active')
            ->orderBy('risk_level')
            ->orderBy('minimum_investment')
            ->get();

        return view('investments.index', compact('portfolioSummary', 'availableProducts'));
    }

    /**
     * Show investment products
     */
    public function products()
    {
        $products = InvestmentProduct::where('status', 'active')
            ->orderBy('product_type')
            ->orderBy('risk_level')
            ->get();

        return view('investments.products', compact('products'));
    }

    /**
     * Show investment product details
     */
    public function showProduct(InvestmentProduct $product)
    {
        $product->load('portfolios');
        
        return view('investments.product-details', compact('product'));
    }

    /**
     * Show investment purchase form
     */
    public function create(InvestmentProduct $product)
    {
        $user = Auth::user();
        
        return view('investments.create', compact('product', 'user'));
    }

    /**
     * Process investment purchase
     */
    public function store(Request $request, InvestmentProduct $product)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . $product->minimum_investment,
            'auto_renewal' => 'boolean',
            'beneficiary_name' => 'nullable|string|max:255',
            'beneficiary_relationship' => 'nullable|string|max:100',
        ]);

        try {
            $user = Auth::user();
            $metadata = [
                'auto_renewal' => $request->boolean('auto_renewal'),
                'beneficiary_name' => $request->beneficiary_name,
                'beneficiary_relationship' => $request->beneficiary_relationship,
                'source_account' => $request->source_account,
                'purchase_channel' => 'web'
            ];

            $portfolio = $this->investmentService->purchaseInvestment(
                $user,
                $product,
                $request->amount,
                $metadata
            );

            return redirect()->route('investments.portfolio', $portfolio)
                ->with('success', 'Investment purchase successful! Certificate Number: ' . $portfolio->certificate_number);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show investment portfolio details
     */
    public function portfolio(InvestmentPortfolio $portfolio)
    {
        $this->authorize('view', $portfolio);
        
        $portfolio->load('product', 'member');
        $returns = $this->investmentService->calculateReturns($portfolio);
        $transactions = InvestmentTransaction::where('portfolio_id', $portfolio->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('investments.portfolio', compact('portfolio', 'returns', 'transactions'));
    }

    /**
     * Show my investments
     */
    public function myInvestments()
    {
        $user = Auth::user();
        $portfolioSummary = $this->investmentService->getPortfolioSummary($user);

        return view('investments.my-investments', compact('portfolioSummary'));
    }

    /**
     * Show withdrawal form
     */
    public function withdrawalForm(InvestmentPortfolio $portfolio)
    {
        $this->authorize('update', $portfolio);
        
        $portfolio->load('product');
        $returns = $this->investmentService->calculateReturns($portfolio);

        return view('investments.withdrawal', compact('portfolio', 'returns'));
    }

    /**
     * Process withdrawal request
     */
    public function processWithdrawal(Request $request, InvestmentPortfolio $portfolio)
    {
        $this->authorize('update', $portfolio);
        
        $request->validate([
            'withdrawal_type' => 'required|in:partial,full',
            'amount' => 'required_if:withdrawal_type,partial|numeric|min:1',
            'reason' => 'nullable|string|max:500',
            'destination_account' => 'nullable|string|max:255',
        ]);

        try {
            $amount = $request->withdrawal_type === 'full' ? null : $request->amount;
            
            $transaction = $this->investmentService->processWithdrawal(
                $portfolio,
                $amount,
                $request->reason
            );

            return redirect()->route('investments.portfolio', $portfolio)
                ->with('success', 'Withdrawal request submitted successfully. Reference: ' . $transaction->reference_number);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show investment transactions
     */
    public function transactions()
    {
        $user = Auth::user();
        $transactions = InvestmentTransaction::where('member_id', $user->id)
            ->with(['portfolio.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('investments.transactions', compact('transactions'));
    }

    /**
     * Show investment statement
     */
    public function statement(InvestmentPortfolio $portfolio)
    {
        $this->authorize('view', $portfolio);
        
        $portfolio->load('product', 'member');
        $returns = $this->investmentService->calculateReturns($portfolio);
        $transactions = InvestmentTransaction::where('portfolio_id', $portfolio->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('investments.statement', compact('portfolio', 'returns', 'transactions'));
    }

    /**
     * Admin: Manage investment products
     */
    public function manageProducts()
    {
        $this->authorize('manage', InvestmentProduct::class);
        
        $products = InvestmentProduct::orderBy('created_at', 'desc')->get();

        return view('admin.investments.products', compact('products'));
    }

    /**
     * Admin: Create investment product
     */
    public function createProduct()
    {
        $this->authorize('create', InvestmentProduct::class);
        
        return view('admin.investments.create-product');
    }

    /**
     * Admin: Store investment product
     */
    public function storeProduct(Request $request)
    {
        $this->authorize('create', InvestmentProduct::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'product_type' => 'required|in:fixed_deposit,money_market,government_bond,equity_fund,balanced_fund,retirement_fund',
            'minimum_investment' => 'required|numeric|min:1',
            'maximum_investment' => 'nullable|numeric|gt:minimum_investment',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'dividend_rate' => 'nullable|numeric|min:0|max:100',
            'risk_level' => 'required|in:low,medium,high',
            'term_months' => 'nullable|integer|min:1|max:120',
            'compounding_frequency' => 'required|in:daily,monthly,quarterly,semi_annually,annually',
            'liquidity_type' => 'required|in:high,medium,low',
            'early_withdrawal_penalty' => 'nullable|numeric|min:0|max:100',
            'management_fee' => 'nullable|numeric|min:0|max:10',
        ]);

        try {
            $product = InvestmentProduct::create($request->all());

            return redirect()->route('admin.investments.products')
                ->with('success', 'Investment product created successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Admin: Process dividend distribution
     */
    public function distributeDividends(Request $request, InvestmentProduct $product)
    {
        $this->authorize('manage', $product);
        
        $request->validate([
            'dividend_rate' => 'required|numeric|min:0|max:100',
            'declaration_date' => 'required|date',
        ]);

        try {
            $distributedCount = $this->investmentService->distributeDividends(
                $product,
                $request->dividend_rate,
                \Carbon\Carbon::parse($request->declaration_date)
            );

            return back()->with('success', "Dividends distributed to {$distributedCount} investors.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Admin: Investment analytics
     */
    public function analytics()
    {
        $this->authorize('viewAny', InvestmentPortfolio::class);
        
        $analytics = [
            'total_investments' => InvestmentPortfolio::sum('investment_amount'),
            'active_portfolios' => InvestmentPortfolio::where('status', 'active')->count(),
            'total_investors' => InvestmentPortfolio::distinct('member_id')->count(),
            'products_performance' => InvestmentProduct::withCount('portfolios')
                ->withSum('portfolios', 'investment_amount')
                ->get(),
            'recent_transactions' => InvestmentTransaction::with(['member', 'product'])
                ->latest()
                ->limit(10)
                ->get(),
        ];

        return view('admin.investments.analytics', compact('analytics'));
    }
} 
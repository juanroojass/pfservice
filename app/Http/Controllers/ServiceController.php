<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ServiceController extends Controller
{
    protected $enterpriseArray = [
        ['company_id' => 1, 'company_name' => 'Google Corp.', 'min_mount' => 10, 'max_mount' => 100, 'product' => ['transaction', 'recurring'] ],  
        ['company_id' => 2, 'company_name' => 'Twitter Corp.', 'min_mount' => 30, 'max_mount' => 150, 'product' => ['recurring']], 
        ['company_id' => 3, 'company_name' => 'Amazon Corp.', 'min_mount' => 50, 'max_mount' => 120, 'product' => ['transaction', 'cash']],
    ];    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->enterpriseArray;        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $validator = \Validator::make($request->all(), [        
                'company_id'   => 'required|integer',
                'amount'   => 'required|numeric',
                'product' => 'required|string',            
            ]);    
            if($validator->fails()){
                return $validator->messages();
            }    
            $key = array_search($request->company_id, array_column($this->enterpriseArray, 'company_id'));     
            $min_mount = $this->enterpriseArray[$key]['min_mount'];
            $max_mount = $this->enterpriseArray[$key]['max_mount'];
            $companyProduct = join(', ', $this->enterpriseArray[$key]['product']);            
            $validationOutputs = [
                'authorized' => "true",   
                'company_name' => "",
                'product' => "",      
                'business_errors' => [],  
            ]; 
            // $productData = null;
            $validateError = false;           
            
            if($key != ''){ // It validates if the company exists   
                if( !($request->amount >= $min_mount && $request->amount <= $max_mount) ){ // It validates the minimum and maximum amount                                        
                    $validationOutputs['authorized'] = "false";
                    $validationOutputs['business_errors'][] = "This company only allows this amount range {$min_mount} - {$max_mount}";
                }
                if (!in_array($request->product, $this->enterpriseArray[$key]['product'])){ // It validates the type of product                    
                    $validationOutputs['authorized'] = "false";
                    $validationOutputs['business_errors'][] = "This company only operates these products '{$companyProduct}'";
                }
                if($validationOutputs['authorized'] == "true"){
                    $productId = array_search($request->product,  $this->enterpriseArray[$key]['product'])+1;
                    $productData = [
                        'id' => "{$productId}",
                        'type' => $request->product,
                        'amount' => $request->amount,
                        'date_operation' => date('Y-m-d H:i:s'),
                    ];
                    $validationOutputs['company_name'] = $this->enterpriseArray[$key]['company_name'];
                    $validationOutputs['product'] = $productData;
                }
            }else{               
                $validationOutputs['authorized'] = "false";
                $validationOutputs['business_errors'][] = "The company does not exist.";              
            }
            return response()->json($validationOutputs);  
            // switched to m1       
            // comentario m2     
        }catch (\Exception $e){       
            $validationOutputs['authorized'] = "false";     
            $validationOutputs['business_errors'][] = $e->getMessage().", the exception was created on line: " . $e->getLine(); 
            return response()->json($validationOutputs, 500);    
        }         
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {  
        $key = array_search($id, array_column($this->enterpriseArray, 'company_id')); 
        if($key != ''){
            return response()->json($this->enterpriseArray[$key]);     
        }
        return response()->json('No company found'); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return 'not available';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return 'not available';
    }
}

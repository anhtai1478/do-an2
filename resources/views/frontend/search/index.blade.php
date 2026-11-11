@extends('frontend.layouts.master')

@section('title', 'Tìm kiếm sản phẩm')

@section('content')
    <div class="features_items">
        <h2 class="title text-center">
            @if ($name !== '')
                Kết quả tìm kiếm: "{{ $name }}" ({{ $products->total() }})
            @else
                Tất cả sản phẩm
            @endif
        </h2>

        <div class="row">
            @forelse ($products as $item)
                @include('frontend.products.card', ['item' => $item])
            @empty
                <p class="text-center">Không tìm thấy sản phẩm nào có tên chứa "{{ $name }}".</p>
            @endforelse
        </div>

        <div class="text-center">
            {{ $products->links() }}
        </div>
    </div>
@endsection


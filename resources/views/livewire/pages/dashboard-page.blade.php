<div>
   <div id="header" class="p-2.5  border-b  border-neutral-300 dark:border-neutral-800 flex items-center justify-between h-11">
       <div>
         <flux:breadcrumbs>
            <flux:breadcrumbs.item href="#">Home</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Blog</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Post</flux:breadcrumbs.item>
        </flux:breadcrumbs>
       </div>
       <div class="pr-2.5 flex items-center space-x-3">
        <div class="flex items-center space-x-2">
            <flux:select size="sm" placeholder="Choose industry..." class="-mt-1.5">
            <flux:select.option>Photography</flux:select.option>
            <flux:select.option>Design services</flux:select.option>
            <flux:select.option>Web development</flux:select.option>
            <flux:select.option>Accounting</flux:select.option>
            <flux:select.option>Legal services</flux:select.option>
            <flux:select.option>Consulting</flux:select.option>
            <flux:select.option>Other</flux:select.option>
        </flux:select>
        {{-- <x-profile-dropdown /> --}}
       </div>
       </div>
   </div>
   <div class="p-2.5 min-h-full bg-white dark:bg-black">
        Content Heres
   </div>
</div>

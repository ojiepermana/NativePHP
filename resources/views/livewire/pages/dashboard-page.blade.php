<div>
   <div id="header" class="p-2.5   flex items-center justify-between h-11">
       <div>
        <flux:heading size="lg">Dashboard</flux:heading>
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
   <div class="p-2.5  bg-white dark:bg-black h-[calc(100vh-6rem)] overflow-auto border-y  border-neutral-300 dark:border-neutral-800">
        Content Heres
   </div>
</div>

<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Kanban Board: <span class="text-blue-600">{{ $project->name }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">Drag and drop cards to organize your workflow</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openAddColumnModal()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-plus mr-2"></i> Add Column
                </button>
                <a href="{{ route('projects.show', $project) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium text-sm rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Project
                </a>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="bg-gray-100 rounded-xl p-6 min-h-[600px] overflow-x-auto">
            <div id="kanbanBoard" class="flex gap-4" style="min-width: max-content;">
                @foreach ($board->columns as $column)
                    <div class="kanban-column bg-white rounded-lg shadow-sm w-80 flex-shrink-0"
                        data-column-id="{{ $column->id }}">
                        <!-- Column Header -->
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between"
                            style="border-left: 4px solid {{ $column->color ?? '#6B7280' }};">
                            <h3 class="font-semibold text-gray-800">{{ $column->name }}</h3>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                    {{ $column->cards->count() }}
                                </span>
                                <button
                                    onclick="openEditColumnModal({{ $column->id }}, '{{ $column->name }}', '{{ $column->color ?? '#6B7280' }}')"
                                    class="text-gray-400 hover:text-gray-600 transition p-1">
                                    <i class="fas fa-ellipsis-v text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Cards Container -->
                        <div class="p-3 space-y-3 min-h-[400px] kanban-cards" data-column-id="{{ $column->id }}">
                            @foreach ($column->cards as $card)
                                <div class="kanban-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition group"
                                    data-card-id="{{ $card->id }}" onclick="openViewCardModal({{ $card->id }})">
                                    @if ($card->color)
                                        <div class="w-full h-1 rounded-full mb-3" style="background-color: {{ $card->color }};">
                                        </div>
                                    @endif
                                    <h4 class="font-medium text-gray-800 text-sm mb-2">{{ $card->title }}</h4>
                                    @if ($card->description)
                                        <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ Str::limit($card->description, 80) }}
                                        </p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if ($card->priority)
                                                <span class="text-xs px-2 py-0.5 rounded-full {{ $card->priority_color }}">
                                                    {{ ucfirst($card->priority) }}
                                                </span>
                                            @endif
                                            @if ($card->due_date)
                                                <span class="text-xs text-gray-500">
                                                    <i class="far fa-calendar mr-1"></i>{{ $card->due_date->format('M d') }}
                                                </span>
                                            @endif
                                            @if ($card->attachments->count() > 0)
                                                <span class="text-xs text-gray-500">
                                                    <i class="fas fa-paperclip mr-1"></i>{{ $card->attachments->count() }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($card->assignees->count() > 0)
                                            <div class="flex -space-x-1">
                                                @foreach ($card->assignees->take(3) as $assignee)
                                                    <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-medium border-2 border-white"
                                                        title="{{ $assignee->name }}">
                                                        {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                                    </div>
                                                @endforeach
                                                @if ($card->assignees->count() > 3)
                                                    <div
                                                        class="w-6 h-6 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-medium border-2 border-white">
                                                        +{{ $card->assignees->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Add Card Button -->
                        <div class="p-3 border-t border-gray-100">
                            <button onclick="openAddCardModal({{ $column->id }})"
                                class="w-full py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition flex items-center justify-center gap-2">
                                <i class="fas fa-plus"></i> Add Card
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Add Column Modal -->
    <div id="addColumnModal" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('addColumnModal')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Column</h3>
                <form id="addColumnForm" onsubmit="submitAddColumn(event)">
                    <input type="hidden" name="board_id" value="{{ $board->id }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Column Name</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <input type="color" name="color" value="#6B7280"
                            class="w-full h-10 rounded-lg cursor-pointer border border-gray-300">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addColumnModal')"
                            class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Add
                            Column</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Column Modal -->
    <div id="editColumnModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('editColumnModal')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Column</h3>
                <form id="editColumnForm" onsubmit="submitEditColumn(event)">
                    <input type="hidden" name="column_id" id="editColumnId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Column Name</label>
                        <input type="text" name="name" id="editColumnName" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <input type="color" name="color" id="editColumnColor"
                            class="w-full h-10 rounded-lg cursor-pointer border border-gray-300">
                    </div>
                    <div class="flex justify-between">
                        <button type="button" onclick="deleteColumn()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeModal('editColumnModal')"
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Card Modal -->
    <div id="addCardModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('addCardModal')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Card</h3>
                <form id="addCardForm" onsubmit="submitAddCard(event)" enctype="multipart/form-data">
                    <input type="hidden" name="column_id" id="addCardColumnId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select name="priority"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input type="date" name="due_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mb-4" x-data="{ open: false, selected: [] }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assignees</label>
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span x-text="selected.length ? selected.length + ' selected' : 'Select assignees'"
                                    class="text-gray-700"></span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                @foreach ($users as $user)
                                    <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="assignees[]" value="{{ $user->id }}"
                                            @change="selected = [...document.querySelectorAll('#addCardForm input[name=\'assignees[]\']:checked')].map(el => el.value)"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $user->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <input type="color" name="color" value="#3B82F6"
                            class="w-full h-10 rounded-lg cursor-pointer border border-gray-300">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                        <input type="file" name="attachments[]" multiple
                            accept=".pdf,.jpg,.jpeg,.png,.gif,.mp4,.mp3,.doc,.docx,.xls,.xlsx,.zip,.rar"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">Allowed: PDF, JPG, PNG, MP4, MP3, Word, Excel, ZIP, RAR
                            (max 20MB each)</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addCardModal')"
                            class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Add
                            Card</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View/Edit Card Modal -->
    <div id="viewCardModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('viewCardModal')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Card</h3>
                <form id="editCardForm" onsubmit="submitEditCard(event)" enctype="multipart/form-data">
                    <input type="hidden" name="card_id" id="editCardId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" id="editCardTitle" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="editCardDescription" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select name="priority" id="editCardPriority"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input type="date" name="due_date" id="editCardDueDate"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mb-4" x-data="{ open: false, selected: [] }" id="editAssigneesContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assignees</label>
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-left flex items-center justify-between focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span x-text="selected.length ? selected.length + ' selected' : 'Select assignees'"
                                    class="text-gray-700"></span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                @foreach ($users as $user)
                                    <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="assignees[]" value="{{ $user->id }}"
                                            class="edit-assignee-checkbox"
                                            @change="selected = [...document.querySelectorAll('.edit-assignee-checkbox:checked')].map(el => el.value)"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $user->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <input type="color" name="color" id="editCardColor"
                            class="w-full h-10 rounded-lg cursor-pointer border border-gray-300">
                    </div>

                    <!-- Existing Attachments -->
                    <div class="mb-4" id="existingAttachments">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Attachments</label>
                        <div id="attachmentsList" class="space-y-2">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Add New Attachments</label>
                        <input type="file" name="attachments[]" multiple
                            accept=".pdf,.jpg,.jpeg,.png,.gif,.mp4,.mp3,.doc,.docx,.xls,.xlsx,.zip,.rar"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-500 mt-1">Allowed: PDF, JPG, PNG, MP4, MP3, Word, Excel, ZIP, RAR
                            (max 20MB each)</p>
                    </div>
                    <div class="flex justify-between">
                        <button type="button" onclick="deleteCard()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeModal('viewCardModal')"
                                class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Attachment Preview Modal -->
    <div id="attachmentPreviewModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75" onclick="closePreviewModal()"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 truncate" id="previewFileName">File Preview</h3>
                    <div class="flex items-center gap-2">
                        <a href="#" id="previewDownloadBtn" download
                            class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                            title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <button onclick="closePreviewModal()"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div id="previewContent"
                    class="flex items-center justify-center bg-gray-100 min-h-[400px] max-h-[70vh] overflow-auto p-4">
                    <!-- Content injected by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        const csrfToken = '{{ csrf_token() }}';
        let cardsData = @json($board->columns->pluck('cards', 'id'));

        // File type icons
        const fileIcons = {
            'jpg': 'fa-image text-blue-500',
            'jpeg': 'fa-image text-blue-500',
            'png': 'fa-image text-blue-500',
            'gif': 'fa-image text-blue-500',
            'pdf': 'fa-file-pdf text-red-500',
            'mp4': 'fa-file-video text-purple-500',
            'mp3': 'fa-file-audio text-green-500',
            'doc': 'fa-file-word text-blue-600',
            'docx': 'fa-file-word text-blue-600',
            'xls': 'fa-file-excel text-green-600',
            'xlsx': 'fa-file-excel text-green-600',
            'zip': 'fa-file-archive text-yellow-600',
            'rar': 'fa-file-archive text-yellow-600',
        };

        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            return fileIcons[ext] || 'fa-file text-gray-500';
        }

        // Initialize Sortable for columns
        new Sortable(document.getElementById('kanbanBoard'), {
            animation: 150,
            handle: '.kanban-column',
            draggable: '.kanban-column',
            ghostClass: 'opacity-50',
            onEnd: function (evt) {
                const columns = [];
                document.querySelectorAll('.kanban-column').forEach((col, index) => {
                    columns.push({
                        id: parseInt(col.dataset.columnId),
                        position: index
                    });
                });
                reorderColumns(columns);
            }
        });

        // Initialize Sortable for each card container
        document.querySelectorAll('.kanban-cards').forEach(cardContainer => {
            new Sortable(cardContainer, {
                group: 'cards',
                animation: 150,
                draggable: '.kanban-card',
                ghostClass: 'opacity-50',
                onEnd: function (evt) {
                    const cardId = parseInt(evt.item.dataset.cardId);
                    const newColumnId = parseInt(evt.to.dataset.columnId);
                    const newPosition = evt.newIndex;
                    moveCard(cardId, newColumnId, newPosition);
                }
            });
        });

        // Modal functions
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openAddColumnModal() {
            document.getElementById('addColumnForm').reset();
            openModal('addColumnModal');
        }

        function openEditColumnModal(id, name, color) {
            document.getElementById('editColumnId').value = id;
            document.getElementById('editColumnName').value = name;
            document.getElementById('editColumnColor').value = color;
            openModal('editColumnModal');
        }

        function openAddCardModal(columnId) {
            document.getElementById('addCardForm').reset();
            document.getElementById('addCardColumnId').value = columnId;
            openModal('addCardModal');
        }

        function openViewCardModal(cardId) {
            // Fetch fresh data from server
            fetch(`/kanban/cards/${cardId}`)
                .then(res => res.json())
                .then(data => populateEditCardModal(data.card))
                .catch(err => {
                    console.error(err);
                    alert('Failed to load card data');
                });
        }

        function populateEditCardModal(card) {
            document.getElementById('editCardId').value = card.id;
            document.getElementById('editCardTitle').value = card.title || '';
            document.getElementById('editCardDescription').value = card.description || '';
            document.getElementById('editCardPriority').value = card.priority || 'medium';
            document.getElementById('editCardDueDate').value = card.due_date ? card.due_date.split('T')[0] : '';
            document.getElementById('editCardColor').value = card.color || '#3B82F6';

            // Set multiple assignees using checkboxes
            const assigneeCheckboxes = document.querySelectorAll('.edit-assignee-checkbox');
            const assigneeIds = card.assignees ? card.assignees.map(a => a.id) : [];
            assigneeCheckboxes.forEach(checkbox => {
                checkbox.checked = assigneeIds.includes(parseInt(checkbox.value));
            });
            // Update Alpine.js selected state
            const container = document.getElementById('editAssigneesContainer');
            if (container && container.__x) {
                container.__x.$data.selected = assigneeIds.map(String);
            }

            // Populate attachments
            const attachmentsList = document.getElementById('attachmentsList');
            if (card.attachments && card.attachments.length > 0) {
                document.getElementById('existingAttachments').style.display = 'block';
                attachmentsList.innerHTML = card.attachments.map(att => {
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(att.file_name.split('.').pop().toLowerCase());
                    const isPdf = att.file_name.toLowerCase().endsWith('.pdf');
                    const isVideo = ['mp4', 'avi', 'mov', 'wmv'].includes(att.file_name.split('.').pop().toLowerCase());
                    const canPreview = isImage || isPdf || isVideo;

                    return `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100" id="attachment-${att.id}">
                        <div class="flex items-center gap-3 overflow-hidden flex-1">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                <i class="fas ${getFileIcon(att.file_name)} text-lg"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800 truncate" title="${att.file_name}">${att.file_name}</p>
                                <p class="text-xs text-gray-400">${(att.file_size / 1024).toFixed(1)} KB</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 ml-2">
                            ${canPreview ? `
                            <button type="button" onclick="previewAttachment('/storage/${att.file_path}', '${att.file_name}')" 
                                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            ` : ''}
                            <a href="/storage/${att.file_path}" download="${att.file_name}"
                                class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition" title="Download">
                                <i class="fas fa-download"></i>
                            </a>
                            <button type="button" onclick="deleteAttachment(${att.id})" 
                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `}).join('');
            } else {
                document.getElementById('existingAttachments').style.display = 'none';
                attachmentsList.innerHTML = '<p class="text-sm text-gray-400 italic">No attachments</p>';
            }

            openModal('viewCardModal');
        }

        async function deleteAttachment(attachmentId) {
            const result = await Swal.fire({
                title: 'Delete Attachment?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) return;

            const response = await fetch(`/kanban/attachments/${attachmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                document.getElementById(`attachment-${attachmentId}`).remove();
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Attachment has been deleted.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', 'Failed to delete attachment', 'error');
            }
        }

        function previewAttachment(url, filename) {
            const modal = document.getElementById('attachmentPreviewModal');
            const content = document.getElementById('previewContent');
            const fileNameEl = document.getElementById('previewFileName');
            const downloadBtn = document.getElementById('previewDownloadBtn');

            fileNameEl.textContent = filename;
            downloadBtn.href = url;
            downloadBtn.setAttribute('download', filename);

            const ext = filename.split('.').pop().toLowerCase();

            // Clear previous content
            content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i></div>';

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                content.innerHTML = `<img src="${url}" alt="${filename}" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-lg">`;
            } else if (ext === 'pdf') {
                content.innerHTML = `<iframe src="${url}" class="w-full h-[60vh] rounded-lg" frameborder="0"></iframe>`;
            } else if (['mp4', 'avi', 'mov', 'wmv'].includes(ext)) {
                content.innerHTML = `<video src="${url}" controls class="max-w-full max-h-[60vh] rounded-lg shadow-lg"><p>Your browser does not support video playback.</p></video>`;
            } else if (['mp3', 'wav', 'ogg'].includes(ext)) {
                content.innerHTML = `
                    <div class="text-center p-8">
                        <i class="fas fa-file-audio text-6xl text-green-500 mb-4"></i>
                        <audio src="${url}" controls class="w-full max-w-md mx-auto"></audio>
                    </div>`;
            } else {
                content.innerHTML = `
                    <div class="text-center p-8">
                        <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">Preview not available for this file type.</p>
                        <a href="${url}" download="${filename}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    </div>`;
            }

            modal.classList.remove('hidden');
        }

        function closePreviewModal() {
            document.getElementById('attachmentPreviewModal').classList.add('hidden');
            document.getElementById('previewContent').innerHTML = '';
        }

        // AJAX functions
        async function submitAddColumn(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('{{ route('kanban.columns.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (response.ok) {
                location.reload();
            } else {
                alert('Failed to create column');
            }
        }

        async function submitEditColumn(e) {
            e.preventDefault();
            const columnId = document.getElementById('editColumnId').value;
            const name = document.getElementById('editColumnName').value;
            const color = document.getElementById('editColumnColor').value;

            const response = await fetch(`/kanban/columns/${columnId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name, color }),
            });

            if (response.ok) {
                location.reload();
            } else {
                alert('Failed to update column');
            }
        }

        async function deleteColumn() {
            const result = await Swal.fire({
                title: 'Delete Column?',
                text: 'This will delete the column and all its cards. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) return;

            const columnId = document.getElementById('editColumnId').value;

            const response = await fetch(`/kanban/columns/${columnId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Column has been deleted.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', 'Failed to delete column', 'error');
            }
        }

        async function submitAddCard(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            const response = await fetch('{{ route('kanban.cards.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (response.ok) {
                location.reload();
            } else {
                const error = await response.json();
                alert('Failed to create card: ' + (error.message || 'Unknown error'));
            }
        }

        async function submitEditCard(e) {
            e.preventDefault();
            const cardId = document.getElementById('editCardId').value;
            const form = e.target;
            const formData = new FormData(form);

            const response = await fetch(`/kanban/cards/${cardId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (response.ok) {
                location.reload();
            } else {
                const error = await response.json();
                alert('Failed to update card: ' + (error.message || 'Unknown error'));
            }
        }

        async function deleteCard() {
            const result = await Swal.fire({
                title: 'Delete Card?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) return;

            const cardId = document.getElementById('editCardId').value;

            const response = await fetch(`/kanban/cards/${cardId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Card has been deleted.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', 'Failed to delete card', 'error');
            }
        }

        async function reorderColumns(columns) {
            await fetch('{{ route('kanban.columns.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ columns }),
            });
        }

        async function moveCard(cardId, columnId, position) {
            await fetch('{{ route('kanban.cards.move') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ card_id: cardId, column_id: columnId, position }),
            });
        }
    </script>
</x-app-layout>
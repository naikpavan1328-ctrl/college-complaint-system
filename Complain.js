
        // Sample data for complaints
        const sampleComplaints = [
            {
                id: "CMP-2023-001",
                subject: "Defective Product Received",
                category: "Product Issue",
                date: "2023-05-15",
                status: "resolved",
                description: "I received a damaged laptop with a cracked screen. The packaging was intact, suggesting the product was damaged before shipping.",
                updates: [
                    { date: "2023-05-15", status: "pending", message: "Complaint received and under review." },
                    { date: "2023-05-16", status: "inprogress", message: "Our team is investigating the issue. We've contacted the shipping department." },
                    { date: "2023-05-18", status: "resolved", message: "Replacement product has been shipped. You should receive it within 3-5 business days." }
                ]
            },
            {
                id: "CMP-2023-002",
                subject: "Billing Discrepancy",
                category: "Billing Problem",
                date: "2023-05-20",
                status: "inprogress",
                description: "I was charged twice for my monthly subscription. Please refund the extra charge as soon as possible.",
                updates: [
                    { date: "2023-05-20", status: "pending", message: "Complaint received and under review." },
                    { date: "2023-05-21", status: "inprogress", message: "Our billing department is reviewing your account and transaction history." }
                ]
            },
            {
                id: "CMP-2023-003",
                subject: "Poor Customer Service",
                category: "Staff Behavior",
                date: "2023-05-22",
                status: "pending",
                description: "I had a very unpleasant experience with one of your customer service representatives. They were rude and unhelpful when I called about my account issues.",
                updates: [
                    { date: "2023-05-22", status: "pending", message: "Complaint received and under review." }
                ]
            },
            {
                id: "CMP-2023-004",
                subject: "Late Delivery",
                category: "Delivery Problem",
                date: "2023-05-18",
                status: "rejected",
                description: "My order was supposed to be delivered on May 15th, but I still haven't received it. This is causing significant inconvenience.",
                updates: [
                    { date: "2023-05-18", status: "pending", message: "Complaint received and under review." },
                    { date: "2023-05-19", status: "inprogress", message: "We're tracking your package and investigating the delay." },
                    { date: "2023-05-21", status: "rejected", message: "After investigation, we found that the delivery was attempted but no one was available to receive it. The package is being returned to our warehouse." }
                ]
            }
        ];

        // DOM Elements
        const homeSection = document.getElementById('home-section');
        const submitSection = document.getElementById('submit-section');
        const trackSection = document.getElementById('track-section');
        const complaintForm = document.getElementById('complaint-form');
        const resetFormBtn = document.getElementById('reset-form');
        const complaintsTable = document.getElementById('complaints-table');
        const complaintModal = document.getElementById('complaint-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const closeModalBtnFooter = document.getElementById('close-modal-btn');
        const modalContent = document.getElementById('modal-content');
        const successMessage = document.getElementById('success-message');
        const newComplaintId = document.getElementById('new-complaint-id');
        const viewComplaintBtn = document.getElementById('view-complaint-btn');
        const closeSuccessBtn = document.getElementById('close-success-btn');
        const searchBtn = document.getElementById('search-btn');
        const complaintIdInput = document.getElementById('complaint-id');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        // Navigation links
        const homeLinks = [document.getElementById('home-link'), document.getElementById('home-link-mobile')];
        const submitLinks = [document.getElementById('submit-link'), document.getElementById('submit-link-mobile')];
        const trackLinks = [document.getElementById('track-link'), document.getElementById('track-link-mobile')];
        const getStartedBtn = document.getElementById('get-started-btn');
        const trackBtn = document.getElementById('track-btn');

        // Show section function
        function showSection(section) {
            homeSection.classList.add('hidden');
            submitSection.classList.add('hidden');
            trackSection.classList.add('hidden');
            
            section.classList.remove('hidden');
            
            // Close mobile menu if open
            mobileMenu.classList.add('hidden');
            
            // Scroll to top
            window.scrollTo(0, 0);
        }

        // Navigation event listeners
        homeLinks.forEach(link => {
            if (link) link.addEventListener('click', (e) => {
                e.preventDefault();
                showSection(homeSection);
            });
        });

        submitLinks.forEach(link => {
            if (link) link.addEventListener('click', (e) => {
                e.preventDefault();
                showSection(submitSection);
            });
        });

        trackLinks.forEach(link => {
            if (link) link.addEventListener('click', (e) => {
                e.preventDefault();
                showSection(trackSection);
                loadComplaints();
            });
        });

        getStartedBtn.addEventListener('click', () => {
            showSection(submitSection);
        });

        trackBtn.addEventListener('click', () => {
            showSection(trackSection);
            loadComplaints();
        });

        // Mobile menu toggle
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Load complaints to table
        function loadComplaints() {
            complaintsTable.innerHTML = '';
            
            if (sampleComplaints.length === 0) {
                complaintsTable.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No complaints found</td>
                    </tr>
                `;
                return;
            }
            
            sampleComplaints.forEach(complaint => {
                const row = document.createElement('tr');
                
                // Status badge class
                let statusClass = '';
                let statusText = '';
                
                switch(complaint.status) {
                    case 'pending':
                        statusClass = 'status-pending';
                        statusText = 'Pending';
                        break;
                    case 'inprogress':
                        statusClass = 'status-inprogress';
                        statusText = 'In Progress';
                        break;
                    case 'resolved':
                        statusClass = 'status-resolved';
                        statusText = 'Resolved';
                        break;
                    case 'rejected':
                        statusClass = 'status-rejected';
                        statusText = 'Rejected';
                        break;
                }
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${complaint.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${complaint.subject}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${complaint.category}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${complaint.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <button class="view-details text-blue-600 hover:text-blue-800" data-id="${complaint.id}">View Details</button>
                    </td>
                `;
                
                complaintsTable.appendChild(row);
            });
            
            // Add event listeners to view details buttons
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', () => {
                    const complaintId = button.getAttribute('data-id');
                    showComplaintDetails(complaintId);
                });
            });
        }

        // Show complaint details in modal
        function showComplaintDetails(complaintId) {
            const complaint = sampleComplaints.find(c => c.id === complaintId);
            
            if (!complaint) {
                modalContent.innerHTML = `<p class="text-red-600">Complaint not found</p>`;
                complaintModal.classList.remove('hidden');
                return;
            }
            
            // Status badge class
            let statusClass = '';
            let statusText = '';
            
            switch(complaint.status) {
                case 'pending':
                    statusClass = 'status-pending';
                    statusText = 'Pending';
                    break;
                case 'inprogress':
                    statusClass = 'status-inprogress';
                    statusText = 'In Progress';
                    break;
                case 'resolved':
                    statusClass = 'status-resolved';
                    statusText = 'Resolved';
                    break;
                case 'rejected':
                    statusClass = 'status-rejected';
                    statusText = 'Rejected';
                    break;
            }
            
            let updatesHTML = '';
            complaint.updates.forEach(update => {
                let updateStatusClass = '';
                let updateStatusText = '';
                
                switch(update.status) {
                    case 'pending':
                        updateStatusClass = 'status-pending';
                        updateStatusText = 'Pending';
                        break;
                    case 'inprogress':
                        updateStatusClass = 'status-inprogress';
                        updateStatusText = 'In Progress';
                        break;
                    case 'resolved':
                        updateStatusClass = 'status-resolved';
                        updateStatusText = 'Resolved';
                        break;
                    case 'rejected':
                        updateStatusClass = 'status-rejected';
                        updateStatusText = 'Rejected';
                        break;
                }
                
                updatesHTML += `
                    <div class="border-l-2 border-gray-200 pl-4 py-2">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-500">${update.date}</span>
                            <span class="status-badge ${updateStatusClass} text-xs">${updateStatusText}</span>
                        </div>
                        <p class="text-gray-700">${update.message}</p>
                    </div>
                `;
            });
            
            modalContent.innerHTML = `
                <div class="space-y-6">
                    <div class="flex justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">${complaint.subject}</h4>
                            <p class="text-gray-600">${complaint.category}</p>
                        </div>
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </div>
                    
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Complaint ID</h5>
                        <p class="text-gray-800">${complaint.id}</p>
                    </div>
                    
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Date Submitted</h5>
                        <p class="text-gray-800">${complaint.date}</p>
                    </div>
                    
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Description</h5>
                        <p class="text-gray-800">${complaint.description}</p>
                    </div>
                    
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Updates</h5>
                        <div class="space-y-4 mt-3">
                            ${updatesHTML}
                        </div>
                    </div>
                </div>
            `;
            
            complaintModal.classList.remove('hidden');
        }

        // Close modal
        closeModalBtn.addEventListener('click', () => {
            complaintModal.classList.add('hidden');
        });
        
        closeModalBtnFooter.addEventListener('click', () => {
            complaintModal.classList.add('hidden');
        });

        // Submit complaint form
        complaintForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Generate a new complaint ID
            const newId = `CMP-2023-${String(sampleComplaints.length + 1).padStart(3, '0')}`;
            
            // Get form values
            const subject = document.getElementById('subject').value;
            const category = document.getElementById('category').value;
            const description = document.getElementById('description').value;
            
            // Create new complaint object
            const newComplaint = {
                id: newId,
                subject: subject,
                category: document.getElementById('category').options[document.getElementById('category').selectedIndex].text,
                date: new Date().toISOString().split('T')[0],
                status: 'pending',
                description: description,
                updates: [
                    { 
                        date: new Date().toISOString().split('T')[0], 
                        status: 'pending', 
                        message: "Complaint received and under review." 
                    }
                ]
            };
            
            
            sampleComplaints.unshift(newComplaint);
            
            
            newComplaintId.textContent = newId;
            successMessage.classList.remove('hidden');
            
           
            complaintForm.reset();
        });

        resetFormBtn.addEventListener('click', () => {
            complaintForm.reset();
        });

       
        closeSuccessBtn.addEventListener('click', () => {
            successMessage.classList.add('hidden');
        });

        viewComplaintBtn.addEventListener('click', () => {
            successMessage.classList.add('hidden');
            showSection(trackSection);
            loadComplaints();
            
           
            const newId = newComplaintId.textContent;
            setTimeout(() => {
                showComplaintDetails(newId);
            }, 300);
        });

       
        searchBtn.addEventListener('click', () => {
            const searchId = complaintIdInput.value.trim();
            
            if (!searchId) {
                loadComplaints();
                return;
            }
            
            const filteredComplaints = sampleComplaints.filter(c => 
                c.id.toLowerCase().includes(searchId.toLowerCase())
            );
            
            complaintsTable.innerHTML = '';
            
            if (filteredComplaints.length === 0) {
                complaintsTable.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No complaints found with ID: ${searchId}</td>
                    </tr>
                `;
                return;
            }
            
            filteredComplaints.forEach(complaint => {
                const row = document.createElement('tr');
                
             
                let statusClass = '';
                let statusText = '';
                
                switch(complaint.status) {
                    case 'pending':
                        statusClass = 'status-pending';
                        statusText = 'Pending';
                        break;
                    case 'inprogress':
                        statusClass = 'status-inprogress';
                        statusText = 'In Progress';
                        break;
                    case 'resolved':
                        statusClass = 'status-resolved';
                        statusText = 'Resolved';
                        break;
                    case 'rejected':
                        statusClass = 'status-rejected';
                        statusText = 'Rejected';
                        break;
                }
                
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${complaint.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${complaint.subject}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${complaint.category}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${complaint.date}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <button class="view-details text-blue-600 hover:text-blue-800" data-id="${complaint.id}">View Details</button>
                    </td>
                `;
                
                complaintsTable.appendChild(row);
            });
            
           
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', () => {
                    const complaintId = button.getAttribute('data-id');
                    showComplaintDetails(complaintId);
                });
            });
        });

        
        complaintModal.addEventListener('click', (e) => {
            if (e.target === complaintModal) {
                complaintModal.classList.add('hidden');
            }
        });

   
        successMessage.addEventListener('click', (e) => {
            if (e.target === successMessage) {
                successMessage.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            showSection(homeSection);
        });

(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'960a173023e7032a',t:'MTc1Mjc1ODk5MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();
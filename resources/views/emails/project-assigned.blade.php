<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; padding: 20px; color: #374151; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { border-bottom: 2px solid #f9fafb; padding-bottom: 20px; margin-bottom: 20px; text-align: center; }
        .logo { font-size: 24px; font-weight: bold; color: #4338ca; }
        .content { line-height: 1.6; }
        .project-card { background-color: #f9fafb; padding: 20px; border-radius: 6px; margin: 20px 0; border: 1px solid #e5e7eb; }
        .label { font-weight: 600; color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .value { color: #111827; font-weight: 500; font-size: 16px; margin-bottom: 12px; }
        .button { display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Samagri</div>
        </div>
        <div class="content">
            <h2>Hello!</h2>
            <p>You have been assigned to a new project. Here are the details:</p>
            
            <div class="project-card">
                <div class="label">Project Name</div>
                <div class="value">{{ $project->name }}</div>
                
                <div class="label">Client</div>
                <div class="value">{{ $project->client->name ?? 'N/A' }}</div>
                
                <div class="label">Deadline</div>
                <div class="value">{{ $project->deadline ? $project->deadline->format('M d, Y') : 'No Deadline' }}</div>
                
                <div class="label">Description</div>
                <div class="value" style="font-size: 14px; color: #4b5563;">{{ $project->description ?? 'No description provided.' }}</div>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('projects.show', $project->id) }}" class="button">View Project</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Samagri. All rights reserved.
        </div>
    </div>
</body>
</html>
